<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSiteSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class ManageSiteFooterSettings extends Page
{
    use InteractsWithSiteSettings;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3BottomLeft;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.site_section_footer');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.site_section_footer');
    }

    public function mount(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $this->form->fill($record->only([
            'footer_body_en',
            'footer_body_ar',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('footer_body_en')
                    ->label(__('admin_trans.footer_body_en'))
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('footer_body_ar')
                    ->label(__('admin_trans.footer_body_ar'))
                    ->rows(5)
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
        $record->update(Arr::only($data, [
            'footer_body_en',
            'footer_body_ar',
        ]));

        $this->forgetSiteLayoutCache($record);

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();

        $this->form->fill($record->fresh()->only([
            'footer_body_en',
            'footer_body_ar',
        ]));
    }

    protected function getSiteSettingsFormElementId(): string
    {
        return 'site-settings-footer-form';
    }
}
