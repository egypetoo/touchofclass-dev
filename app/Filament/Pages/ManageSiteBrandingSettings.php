<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSiteSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class ManageSiteBrandingSettings extends Page
{
    use InteractsWithSiteSettings;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.site_section_branding');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.site_section_branding');
    }

    public function mount(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $this->form->fill($record->only([
            'site_name_en',
            'site_name_ar',
            'default_locale',
            'logo_en_path',
            'logo_ar_path',
            'favicon_path',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        $uploadDir = 'site/'.($this->getOrganization()?->id ?? 'global');
        $localeOptions = collect(config('laravellocalization.supportedLocales', []))
            ->mapWithKeys(fn (array $meta, string $code): array => [
                $code => ($meta['native'] ?? $meta['name'] ?? $code).' ('.$code.')',
            ])
            ->all();

        return $schema
            ->components([
                TextInput::make('site_name_en')
                    ->label(__('admin_trans.site_name_en'))
                    ->maxLength(255),
                TextInput::make('site_name_ar')
                    ->label(__('admin_trans.site_name_ar'))
                    ->maxLength(255),
                Select::make('default_locale')
                    ->label(__('admin_trans.default_locale'))
                    ->helperText(__('admin_trans.default_locale_help'))
                    ->options($localeOptions)
                    ->required()
                    ->native(false),
                FileUpload::make('logo_en_path')
                    ->label(__('admin_trans.logo_en'))
                    ->image()
                    ->disk('public')
                    ->directory($uploadDir)
                    ->visibility('public')
                    ->nullable(),
                FileUpload::make('logo_ar_path')
                    ->label(__('admin_trans.logo_ar'))
                    ->image()
                    ->disk('public')
                    ->directory($uploadDir)
                    ->visibility('public')
                    ->nullable(),
                FileUpload::make('favicon_path')
                    ->label(__('admin_trans.favicon'))
                    ->image()
                    ->disk('public')
                    ->directory($uploadDir)
                    ->visibility('public')
                    ->nullable(),
            ]);
    }

    public function save(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $data = $this->form->getState();
        $record->update(Arr::only($data, [
            'site_name_en',
            'site_name_ar',
            'default_locale',
            'logo_en_path',
            'logo_ar_path',
            'favicon_path',
        ]));

        $this->forgetSiteLayoutCache($record);

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();

        $this->form->fill($record->fresh()->only([
            'site_name_en',
            'site_name_ar',
            'default_locale',
            'logo_en_path',
            'logo_ar_path',
            'favicon_path',
        ]));
    }

    protected function getSiteSettingsFormElementId(): string
    {
        return 'site-settings-branding-form';
    }
}
