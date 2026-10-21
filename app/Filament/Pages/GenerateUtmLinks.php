<?php

namespace App\Filament\Pages;

use App\Models\Blog;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Services\Marketing\UtmAttribution;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GenerateUtmLinks extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.utm_link_builder');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Company';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.utm_link_builder');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('admin_trans.utm_link_builder_help');
    }

    public function mount(): void
    {
        $this->form->fill([
            'destination' => 'contact-us',
            'record_id' => null,
            'utm_source' => 'snapchat',
            'utm_medium' => 'social',
            'utm_campaign' => '',
            'utm_term' => '',
            'utm_content' => '',
            'generated_url' => '',
        ]);

        $this->refreshGeneratedUrl();
    }

    public function form(Schema $schema): Schema
    {
        $utm = app(UtmAttribution::class);

        return $schema
            ->components([
                Section::make(__('admin_trans.utm_link_builder_section'))
                    ->description(__('admin_trans.utm_link_builder_section_help'))
                    ->schema([
                        Select::make('destination')
                            ->label(__('admin_trans.utm_destination'))
                            ->options($utm->destinationOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set): void {
                                $set('record_id', null);
                                $this->refreshGeneratedUrl();
                            }),
                        Select::make('record_id')
                            ->label(fn ($get): string => match ((string) $get('destination')) {
                                'service' => __('admin_trans.utm_select_service'),
                                'product' => __('admin_trans.utm_select_product'),
                                'blog' => __('admin_trans.utm_select_blog'),
                                'project' => __('admin_trans.utm_select_project'),
                                default => __('admin_trans.utm_select_record'),
                            })
                            ->options(fn ($get): array => $this->recordOptions((string) $get('destination')))
                            ->searchable()
                            ->preload()
                            ->required(fn ($get): bool => in_array((string) $get('destination'), $utm->destinationsRequiringRecord(), true))
                            ->visible(fn ($get): bool => in_array((string) $get('destination'), $utm->destinationsRequiringRecord(), true))
                            ->live()
                            ->afterStateUpdated(fn () => $this->refreshGeneratedUrl()),
                        Select::make('utm_source')
                            ->label(__('admin_trans.utm_platform'))
                            ->options($utm->platformOptions())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->refreshGeneratedUrl()),
                        TextInput::make('utm_medium')
                            ->label(__('admin_trans.utm_medium'))
                            ->placeholder('social')
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => $this->refreshGeneratedUrl()),
                        TextInput::make('utm_campaign')
                            ->label(__('admin_trans.utm_campaign'))
                            ->placeholder('ramadan_2026')
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => $this->refreshGeneratedUrl()),
                        TextInput::make('utm_term')
                            ->label(__('admin_trans.utm_term'))
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => $this->refreshGeneratedUrl()),
                        TextInput::make('utm_content')
                            ->label(__('admin_trans.utm_content'))
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => $this->refreshGeneratedUrl()),
                        TextInput::make('generated_url')
                            ->label(__('admin_trans.utm_generated_url'))
                            ->readOnly()
                            ->dehydrated(false)
                            ->copyable()
                            ->columnSpanFull(),
                        Placeholder::make('utm_example')
                            ->label(__('admin_trans.utm_example_label'))
                            ->content(fn (): HtmlString => new HtmlString(
                                e(__('admin_trans.utm_example_body'))
                            ))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('utm-link-builder-form')
                    ->footer([
                        Actions::make([
                            Action::make('copy')
                                ->label(__('admin_trans.utm_copy_link'))
                                ->icon(Heroicon::OutlinedClipboardDocument)
                                ->action('copyGeneratedUrl'),
                        ])->alignment(Alignment::Start),
                    ]),
            ]);
    }

    /**
     * @return array<int|string, string>
     */
    protected function recordOptions(string $destination): array
    {
        return match ($destination) {
            'service' => Service::query()
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->mapWithKeys(fn (Service $service): array => [
                    $service->id => $service->getTranslation('title', app()->getLocale()) ?: "#{$service->id}",
                ])
                ->all(),
            'product' => Product::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->mapWithKeys(fn (Product $product): array => [
                    $product->id => $product->getTranslation('name', app()->getLocale()) ?: "#{$product->id}",
                ])
                ->all(),
            'blog' => Blog::query()
                ->where('is_active', true)
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->mapWithKeys(fn (Blog $blog): array => [
                    $blog->id => $blog->getTranslation('title', app()->getLocale()) ?: "#{$blog->id}",
                ])
                ->all(),
            'project' => Project::query()
                ->where('is_active', true)
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->mapWithKeys(fn (Project $project): array => [
                    $project->id => $project->getTranslation('title', app()->getLocale()) ?: "#{$project->id}",
                ])
                ->all(),
            default => [],
        };
    }

    public function refreshGeneratedUrl(): void
    {
        $state = $this->data ?? [];
        $destination = (string) ($state['destination'] ?? 'contact-us');
        $recordId = $state['record_id'] ?? null;

        if (in_array($destination, app(UtmAttribution::class)->destinationsRequiringRecord(), true) && blank($recordId)) {
            $this->data['generated_url'] = '';

            return;
        }

        $url = app(UtmAttribution::class)->buildCampaignUrl(
            destination: $destination,
            source: (string) ($state['utm_source'] ?? ''),
            medium: filled($state['utm_medium'] ?? null) ? (string) $state['utm_medium'] : null,
            campaign: filled($state['utm_campaign'] ?? null) ? (string) $state['utm_campaign'] : null,
            term: filled($state['utm_term'] ?? null) ? (string) $state['utm_term'] : null,
            content: filled($state['utm_content'] ?? null) ? (string) $state['utm_content'] : null,
            recordId: filled($recordId) ? $recordId : null,
        );

        $this->data['generated_url'] = $url;
    }

    public function copyGeneratedUrl(): void
    {
        $this->refreshGeneratedUrl();

        Notification::make()
            ->success()
            ->title(__('admin_trans.utm_link_ready'))
            ->body($this->data['generated_url'] ?? '')
            ->send();
    }
}
