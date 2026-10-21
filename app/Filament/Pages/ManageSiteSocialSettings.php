<?php

namespace App\Filament\Pages;

use App\Enums\SocialLinkPlatform;
use App\Filament\Pages\Concerns\InteractsWithSiteSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ManageSiteSocialSettings extends Page
{
    use InteractsWithSiteSettings;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.site_section_social');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.site_section_social');
    }

    public function mount(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $record->load('socialLinks');

        $this->form->fill([
            'social_links' => $record->socialLinks
                ->map(fn ($link) => [
                    'platform' => $link->platform,
                    'url' => $link->url,
                    'is_active' => $link->is_active,
                ])
                ->values()
                ->all(),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('social_links')
                    ->label(__('admin_trans.social_links'))
                    ->schema([
                        Select::make('platform')
                            ->label(__('admin_trans.social_platform'))
                            ->options(SocialLinkPlatform::class)
                            ->required()
                            ->native(false),
                        TextInput::make('url')
                            ->label(__('admin_trans.button_link_url'))
                            ->url()
                            ->required()
                            ->maxLength(2048),
                        Toggle::make('is_active')
                            ->label(__('admin_trans.is_active'))
                            ->default(true),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(0)
                    ->addActionLabel(__('admin_trans.social_link_add'))
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $data = $this->form->getState();
        $socialLinks = $data['social_links'] ?? [];

        $record->socialLinks()->delete();

        foreach (array_values($socialLinks) as $index => $link) {
            $url = trim((string) ($link['url'] ?? ''));
            if ($url === '') {
                continue;
            }

            $rawPlatform = $link['platform'] ?? SocialLinkPlatform::Other;
            $platform = $rawPlatform instanceof SocialLinkPlatform
                ? $rawPlatform->value
                : (string) $rawPlatform;

            $record->socialLinks()->create([
                'platform' => $platform,
                'url' => $url,
                'sort_order' => $index,
                'is_active' => (bool) ($link['is_active'] ?? true),
            ]);
        }

        $this->forgetSiteLayoutCache($record);

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();

        $record->load('socialLinks');
        $this->form->fill([
            'social_links' => $record->socialLinks
                ->map(fn ($l) => [
                    'platform' => $l->platform,
                    'url' => $l->url,
                    'is_active' => $l->is_active,
                ])
                ->values()
                ->all(),
        ]);
    }

    protected function getSiteSettingsFormElementId(): string
    {
        return 'site-settings-social-form';
    }
}
