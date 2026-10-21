<?php

namespace App\Filament\Pages\Concerns;

use App\Models\Organization;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Cache;

trait InteractsWithSiteSettings
{
    public function defaultForm(Schema $schema): Schema
    {
        $record = $this->getSiteSettingRecord();

        if ($record === null) {
            return $schema
                ->columns(1)
                ->statePath('data');
        }

        return $schema
            ->columns(1)
            ->model($record)
            ->operation('edit')
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id($this->getSiteSettingsFormElementId())
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])
                            ->alignment(Alignment::Start),
                    ]),
            ]);
    }

    protected function getSiteSettingRecord(): ?SiteSetting
    {
        $organization = $this->getOrganization();

        return SiteSetting::firstOrCreate(
            ['organization_id' => $organization?->id],
            []
        );
    }

    protected function getOrganization(): ?Organization
    {
        /** @var Organization|null $tenant */
        $tenant = Filament::getTenant();

        return $tenant ?? Organization::query()->orderBy('created_at')->first();
    }

    protected function forgetSiteLayoutCache(?SiteSetting $record = null): void
    {
        Cache::forget('site_settings_layout');
        Cache::forget('site_default_locale');
        Cache::forget('site_coming_soon_enabled');
        Cache::forget('site_settings_coming_soon');
    }

    abstract protected function getSiteSettingsFormElementId(): string;
}
