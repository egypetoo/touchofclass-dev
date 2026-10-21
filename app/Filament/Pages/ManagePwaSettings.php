<?php

namespace App\Filament\Pages;

use App\Models\PwaSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ManagePwaSettings extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 12;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.pwa_settings');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.pwa_settings');
    }

    public function mount(): void
    {
        $record = PwaSetting::query()->firstOrCreate(['id' => 1]);
        $this->form->fill($record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('is_active')->label(__('admin_trans.publish_status'))->default(true),
            TextInput::make('name_ar')->label(__('admin_trans.site_name_ar'))->maxLength(255),
            TextInput::make('name_en')->label(__('admin_trans.site_name_en'))->maxLength(255),
            TextInput::make('short_name_ar')->label(__('admin_trans.pwa_short_name_ar'))->maxLength(32),
            TextInput::make('short_name_en')->label(__('admin_trans.pwa_short_name_en'))->maxLength(32),
            Textarea::make('description_ar')->label(__('admin_trans.pwa_description_ar'))->rows(2),
            Textarea::make('description_en')->label(__('admin_trans.pwa_description_en'))->rows(2),
            ColorPicker::make('theme_color')->label(__('admin_trans.pwa_theme_color')),
            ColorPicker::make('background_color')->label(__('admin_trans.pwa_background_color')),
            TextInput::make('start_url')->label(__('admin_trans.pwa_start_url'))->helperText(__('admin_trans.pwa_start_url_help')),
            Select::make('display')->label(__('admin_trans.pwa_display'))->options([
                'standalone' => 'standalone',
                'fullscreen' => 'fullscreen',
                'minimal-ui' => 'minimal-ui',
                'browser' => 'browser',
            ])->default('standalone'),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->columns(2)->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('pwa-settings-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ])->alignment(Alignment::Start),
                ]),
        ]);
    }

    public function save(): void
    {
        $record = PwaSetting::query()->firstOrCreate(['id' => 1]);
        $record->update($this->form->getState());

        Notification::make()->success()->title(__('admin_trans.success_saved'))->send();
        $this->form->fill($record->fresh()->toArray());
    }
}
