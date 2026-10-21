<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSiteSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
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

class ManageSiteContactSettings extends Page
{
    use InteractsWithSiteSettings;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.site_section_contact');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.site_section_contact');
    }

    public function mount(): void
    {
        $record = $this->getSiteSettingRecord();
        if ($record === null) {
            return;
        }

        $this->form->fill([
            'cta_phone' => $this->entriesForRepeater($record->ctaPhoneEntries()),
            'cta_whatsapp' => $this->entriesForRepeater($record->ctaWhatsappEntries()),
            'cta_label_en' => $record->cta_label_en,
            'cta_label_ar' => $record->cta_label_ar,
            'support_email' => $record->support_email,
            'fax' => $record->fax(),
            'notification_emails' => $record->notification_emails,
            'turnstile_enabled' => (bool) $record->turnstile_enabled,
            'turnstile_site_key' => $record->turnstile_site_key,
            'turnstile_secret_key' => $record->turnstile_secret_key,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('cta_phone')
                    ->label(__('admin_trans.cta_phones'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('admin_trans.cta_country_code'))
                            ->prefix('+')
                            ->placeholder('20')
                            ->numeric()
                            ->required()
                            ->maxLength(5),
                        TextInput::make('number')
                            ->label(__('admin_trans.cta_local_number'))
                            ->tel()
                            ->required()
                            ->maxLength(32)
                            ->helperText(__('admin_trans.cta_local_number_help')),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $this->repeaterItemLabel($state))
                    ->addActionLabel(__('admin_trans.cta_phone_add')),
                Repeater::make('cta_whatsapp')
                    ->label(__('admin_trans.cta_whatsapp_numbers'))
                    ->helperText(__('admin_trans.cta_whatsapp_help'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('admin_trans.cta_country_code'))
                            ->prefix('+')
                            ->placeholder('20')
                            ->numeric()
                            ->required()
                            ->maxLength(5),
                        TextInput::make('number')
                            ->label(__('admin_trans.cta_local_number'))
                            ->tel()
                            ->required()
                            ->maxLength(32)
                            ->helperText(__('admin_trans.cta_local_number_help')),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $this->repeaterItemLabel($state))
                    ->addActionLabel(__('admin_trans.cta_whatsapp_add')),
                TextInput::make('cta_label_en')
                    ->label(__('admin_trans.cta_label_en'))
                    ->maxLength(255),
                TextInput::make('cta_label_ar')
                    ->label(__('admin_trans.cta_label_ar'))
                    ->maxLength(255),
                TextInput::make('support_email')
                    ->label(__('admin_trans.support_email'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('fax')
                    ->label(__('admin_trans.fax'))
                    ->maxLength(64),
                Textarea::make('notification_emails')
                    ->label(__('admin_trans.notification_emails'))
                    ->rows(3)
                    ->helperText(__('admin_trans.notification_emails_help')),
                Section::make(__('admin_trans.turnstile_section'))
                    ->description(__('admin_trans.turnstile_section_help'))
                    ->schema([
                        Toggle::make('turnstile_enabled')
                            ->label(__('admin_trans.turnstile_enabled'))
                            ->helperText(__('admin_trans.turnstile_enabled_help'))
                            ->live()
                            ->default(false),
                        TextInput::make('turnstile_site_key')
                            ->label(__('admin_trans.turnstile_site_key'))
                            ->maxLength(255)
                            ->visible(fn ($get) => (bool) $get('turnstile_enabled'))
                            ->required(fn ($get) => (bool) $get('turnstile_enabled')),
                        TextInput::make('turnstile_secret_key')
                            ->label(__('admin_trans.turnstile_secret_key'))
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->visible(fn ($get) => (bool) $get('turnstile_enabled'))
                            ->required(fn ($get) => (bool) $get('turnstile_enabled')),
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
            'cta_label_en',
            'cta_label_ar',
            'support_email',
            'notification_emails',
            'turnstile_enabled',
            'turnstile_site_key',
            'turnstile_secret_key',
        ]);
        $payload['cta_phone'] = $this->normalizeSavedEntries($data['cta_phone'] ?? []);
        $payload['cta_whatsapp'] = $this->normalizeSavedEntries($data['cta_whatsapp'] ?? []);
        $payload['turnstile_enabled'] = (bool) ($payload['turnstile_enabled'] ?? false);
        $payload['extra'] = array_merge($record->extra ?? [], ['fax' => $data['fax'] ?? null]);

        if (! $payload['turnstile_enabled']) {
            $payload['turnstile_site_key'] = $payload['turnstile_site_key'] ?? $record->turnstile_site_key;
            $payload['turnstile_secret_key'] = $payload['turnstile_secret_key'] ?? $record->turnstile_secret_key;
        }

        $record->update($payload);

        $this->forgetSiteLayoutCache($record);

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();

        $fresh = $record->fresh();
        $this->form->fill([
            'cta_phone' => $this->entriesForRepeater($fresh->ctaPhoneEntries()),
            'cta_whatsapp' => $this->entriesForRepeater($fresh->ctaWhatsappEntries()),
            'cta_label_en' => $fresh->cta_label_en,
            'cta_label_ar' => $fresh->cta_label_ar,
            'support_email' => $fresh->support_email,
            'fax' => $fresh->fax(),
            'notification_emails' => $fresh->notification_emails,
            'turnstile_enabled' => (bool) $fresh->turnstile_enabled,
            'turnstile_site_key' => $fresh->turnstile_site_key,
            'turnstile_secret_key' => $fresh->turnstile_secret_key,
        ]);
    }

    protected function getSiteSettingsFormElementId(): string
    {
        return 'site-settings-contact-form';
    }

    /**
     * @param  list<array{code: string, number: string, display?: string}>  $entries
     * @return list<array{code: string, number: string}>
     */
    protected function entriesForRepeater(array $entries): array
    {
        return collect($entries)
            ->map(fn (array $entry) => [
                'code' => (string) ($entry['code'] ?? ''),
                'number' => (string) ($entry['number'] ?? ''),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{code: string, number: string}>|null
     */
    protected function normalizeSavedEntries(mixed $items): ?array
    {
        $entries = collect(is_array($items) ? $items : [])
            ->map(function ($item) {
                if (! is_array($item)) {
                    $digits = preg_replace('/\D+/', '', (string) $item) ?? '';

                    return $digits === '' ? null : [
                        'code' => '',
                        'number' => $digits,
                    ];
                }

                $code = preg_replace('/\D+/', '', (string) ($item['code'] ?? '')) ?? '';
                $number = preg_replace('/\D+/', '', (string) ($item['number'] ?? '')) ?? '';
                $number = ltrim($number, '0');

                if ($code === '' && $number === '') {
                    return null;
                }

                return [
                    'code' => $code,
                    'number' => $number,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return $entries === [] ? null : $entries;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    protected function repeaterItemLabel(array $state): ?string
    {
        $code = preg_replace('/\D+/', '', (string) ($state['code'] ?? '')) ?? '';
        $number = preg_replace('/\D+/', '', (string) ($state['number'] ?? '')) ?? '';

        if ($code === '' && $number === '') {
            return null;
        }

        return $code !== '' ? '+'.$code.' '.$number : $number;
    }
}
