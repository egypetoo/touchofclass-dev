<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSiteSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class ManageComingSoonSettings extends Page
{
    use InteractsWithSiteSettings;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.site_section_coming_soon');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.site_section_coming_soon');
    }

    public function mount(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $this->form->fill($record->only([
            'coming_soon_enabled',
            'coming_soon_title_en',
            'coming_soon_title_ar',
            'coming_soon_message_en',
            'coming_soon_message_ar',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin_trans.coming_soon_section'))
                    ->description(__('admin_trans.coming_soon_section_help'))
                    ->schema([
                        Toggle::make('coming_soon_enabled')
                            ->label(__('admin_trans.coming_soon_enabled'))
                            ->helperText(__('admin_trans.coming_soon_enabled_help'))
                            ->live()
                            ->default(false),
                        TextInput::make('coming_soon_title_en')
                            ->label(__('admin_trans.coming_soon_title_en'))
                            ->maxLength(255)
                            ->visible(fn ($get) => (bool) $get('coming_soon_enabled')),
                        TextInput::make('coming_soon_title_ar')
                            ->label(__('admin_trans.coming_soon_title_ar'))
                            ->maxLength(255)
                            ->visible(fn ($get) => (bool) $get('coming_soon_enabled')),
                        Textarea::make('coming_soon_message_en')
                            ->label(__('admin_trans.coming_soon_message_en'))
                            ->rows(4)
                            ->visible(fn ($get) => (bool) $get('coming_soon_enabled')),
                        Textarea::make('coming_soon_message_ar')
                            ->label(__('admin_trans.coming_soon_message_ar'))
                            ->rows(4)
                            ->visible(fn ($get) => (bool) $get('coming_soon_enabled')),
                    ]),
            ]);
    }

    public function save(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $data = $this->form->getState();
        $payload = Arr::only($data, [
            'coming_soon_enabled',
            'coming_soon_title_en',
            'coming_soon_title_ar',
            'coming_soon_message_en',
            'coming_soon_message_ar',
        ]);
        $payload['coming_soon_enabled'] = (bool) ($payload['coming_soon_enabled'] ?? false);

        $record->update($payload);

        $this->forgetSiteLayoutCache($record);

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();

        $fresh = $record->fresh();
        $this->form->fill($fresh->only([
            'coming_soon_enabled',
            'coming_soon_title_en',
            'coming_soon_title_ar',
            'coming_soon_message_en',
            'coming_soon_message_ar',
        ]));
    }

    protected function getSiteSettingsFormElementId(): string
    {
        return 'site-settings-coming-soon-form';
    }
}
