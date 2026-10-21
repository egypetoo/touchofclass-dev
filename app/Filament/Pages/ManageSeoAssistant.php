<?php

namespace App\Filament\Pages;

use App\Models\SeoAssistant;
use App\Services\Seo\BuildSitemapService;
use App\Services\Seo\SeoAiService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ManageSeoAssistant extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.seo_assistant');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.seo_assistant');
    }

    public function mount(): void
    {
        $record = $this->getSeoRecord();
        $data = $record->toArray();
        $data['sitemap_sections'] = $record->sitemapSections();
        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        $pages = [
            'home' => __('admin_trans.seo_page_home'),
            'about' => __('admin_trans.seo_page_about'),
            'contact' => __('admin_trans.seo_page_contact'),
            'blog' => __('admin_trans.seo_page_blog'),
            'service' => __('admin_trans.seo_page_service'),
            'products' => __('admin_trans.seo_page_products'),
            'projects' => __('admin_trans.seo_page_projects'),
            'gallery' => __('admin_trans.seo_page_gallery'),
            'branches' => __('admin_trans.seo_page_branches'),
        ];

        $tabs = [];
        foreach ($pages as $key => $label) {
            $tabs[] = Tab::make($label)->schema([
                TextInput::make("{$key}_meta_title_en")->label(__('admin_trans.meta_title_en'))->maxLength(255),
                Textarea::make("{$key}_meta_desc_en")->label(__('admin_trans.meta_desc_en'))->rows(3),
                TextInput::make("{$key}_meta_keywords_en")->label(__('admin_trans.meta_keywords_en'))->maxLength(255),
                TextInput::make("{$key}_meta_robots_en")->label(__('admin_trans.meta_robots_en'))->default('index, follow'),
                TextInput::make("{$key}_meta_title_ar")->label(__('admin_trans.meta_title_ar'))->maxLength(255),
                Textarea::make("{$key}_meta_desc_ar")->label(__('admin_trans.meta_desc_ar'))->rows(3),
                TextInput::make("{$key}_meta_keywords_ar")->label(__('admin_trans.meta_keywords_ar'))->maxLength(255),
                TextInput::make("{$key}_meta_robots_ar")->label(__('admin_trans.meta_robots_ar'))->default('index, follow'),
            ]);
        }

        $tabs[] = Tab::make(__('admin_trans.seo_analytics'))->schema([
            TextInput::make('google_analytics_id')->label(__('admin_trans.google_analytics_id'))->placeholder('G-XXXXXXXXXX'),
            TextInput::make('google_tag_manager_id')->label(__('admin_trans.google_tag_manager_id'))->placeholder('GTM-XXXXXXX'),
            TextInput::make('google_search_console_verification')
                ->label(__('admin_trans.google_search_console_verification'))
                ->helperText(__('admin_trans.google_search_console_verification_help'))
                ->placeholder('google-site-verification content…'),
            Placeholder::make('sitemap_url')
                ->label(__('admin_trans.sitemap_url'))
                ->content(fn (): HtmlString => new HtmlString($this->sitemapUrlHtml())),
            Section::make(__('admin_trans.sitemap_sections'))
                ->description(__('admin_trans.sitemap_sections_help'))
                ->columns(2)
                ->schema([
                    Toggle::make('sitemap_sections.static')
                        ->label(__('admin_trans.sitemap_section_static'))
                        ->default(true)
                        ->live(),
                    Toggle::make('sitemap_sections.services')
                        ->label(__('admin_trans.sitemap_section_services'))
                        ->default(true),
                    Toggle::make('sitemap_sections.blogs')
                        ->label(__('admin_trans.sitemap_section_blogs'))
                        ->default(true),
                    Toggle::make('sitemap_sections.products')
                        ->label(__('admin_trans.sitemap_section_products'))
                        ->default(true),
                    Toggle::make('sitemap_sections.projects')
                        ->label(__('admin_trans.sitemap_section_projects'))
                        ->default(true),
                ]),
            Section::make(__('admin_trans.sitemap_static_pages'))
                ->description(__('admin_trans.sitemap_static_pages_help'))
                ->columns(2)
                ->visible(fn ($get): bool => (bool) $get('sitemap_sections.static'))
                ->schema([
                    Toggle::make('sitemap_sections.pages.home')->label(__('admin_trans.seo_page_home'))->default(true),
                    Toggle::make('sitemap_sections.pages.about')->label(__('admin_trans.seo_page_about'))->default(true),
                    Toggle::make('sitemap_sections.pages.contact')->label(__('admin_trans.seo_page_contact'))->default(true),
                    Toggle::make('sitemap_sections.pages.services')->label(__('admin_trans.seo_page_service'))->default(true),
                    Toggle::make('sitemap_sections.pages.news')->label(__('admin_trans.seo_page_blog'))->default(true),
                    Toggle::make('sitemap_sections.pages.products')->label(__('admin_trans.seo_page_products'))->default(true),
                    Toggle::make('sitemap_sections.pages.projects')->label(__('admin_trans.seo_page_projects'))->default(true),
                    Toggle::make('sitemap_sections.pages.partners')->label(__('admin_trans.partners'))->default(true),
                ]),
        ]);

        return $schema->components([
            Tabs::make('seo_tabs')->tabs($tabs)->columnSpanFull(),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->columns(1)->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('seo-assistant-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                        Action::make('generate_sitemap')
                            ->label(__('admin_trans.generate_sitemap'))
                            ->action('generateSitemap')
                            ->color('gray'),
                        Action::make('generate_seo_ai')
                            ->label(__('admin_trans.generate_seo_ai'))
                            ->color('info')
                            ->form([
                                Select::make('page')
                                    ->label(__('admin_trans.page'))
                                    ->options([
                                        'home' => __('admin_trans.seo_page_home'),
                                        'about' => __('admin_trans.seo_page_about'),
                                        'contact' => __('admin_trans.seo_page_contact'),
                                        'blog' => __('admin_trans.seo_page_blog'),
                                        'service' => __('admin_trans.seo_page_service'),
                                        'products' => __('admin_trans.seo_page_products'),
                                        'projects' => __('admin_trans.seo_page_projects'),
                                        'gallery' => __('admin_trans.seo_page_gallery'),
                                        'branches' => __('admin_trans.seo_page_branches'),
                                    ])
                                    ->required(),
                            ])
                            ->action(function (array $data, SeoAiService $seoAi): void {
                                $result = $seoAi->generateForPage($data['page']);
                                if (! $result['success']) {
                                    Notification::make()
                                        ->danger()
                                        ->title(__('admin_trans.seo_ai_failed'))
                                        ->body($result['error'] ?? '')
                                        ->send();

                                    return;
                                }

                                $state = $this->form->getState();
                                $this->form->fill(array_merge($state, $result['data']));

                                Notification::make()
                                    ->success()
                                    ->title(__('admin_trans.seo_ai_generated'))
                                    ->send();
                            }),
                    ])->alignment(Alignment::Start),
                ]),
        ]);
    }

    public function save(): void
    {
        $this->persistForm();

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();
    }

    public function generateSitemap(BuildSitemapService $service): void
    {
        $this->persistForm();
        $service->generateSitemap();

        $sitemapUrl = $this->sitemapPublicUrl();

        Notification::make()
            ->success()
            ->title(__('admin_trans.sitemap_generated'))
            ->body($sitemapUrl)
            ->actions([
                Action::make('open_sitemap')
                    ->label(__('admin_trans.open_sitemap'))
                    ->url($sitemapUrl, shouldOpenInNewTab: true),
            ])
            ->send();
    }

    protected function sitemapPublicUrl(): string
    {
        return url('/sitemap.xml');
    }

    protected function sitemapUrlHtml(): string
    {
        $url = $this->sitemapPublicUrl();

        if (! is_file(public_path('sitemap.xml'))) {
            return e(__('admin_trans.sitemap_not_generated_yet'));
        }

        return '<a href="'.e($url).'" target="_blank" rel="noopener noreferrer" class="text-primary-600 underline">'.e($url).'</a>';
    }

    protected function persistForm(): void
    {
        $record = $this->getSeoRecord();
        $record->update($this->form->getState());
        $record = $record->fresh();

        $data = $record->toArray();
        $data['sitemap_sections'] = $record->sitemapSections();
        $this->form->fill($data);
    }

    protected function getSeoRecord(): SeoAssistant
    {
        return SeoAssistant::query()->firstOrCreate(['id' => 1]);
    }
}
