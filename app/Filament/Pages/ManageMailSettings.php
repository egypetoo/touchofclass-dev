<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSiteSettings;
use App\Models\MailSetting;
use App\Services\Mail\FormNotificationService;
use App\Services\Mail\MailConfigService;
use BackedEnum;
use Filament\Actions\Action;
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
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ManageMailSettings extends Page
{
    use InteractsWithSiteSettings;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.mail_settings');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.mail_settings');
    }

    public function mount(): void
    {
        $mail = MailSetting::currentOrNew();
        $site = $this->getSiteSettingRecord();

        $this->form->fill([
            'is_active' => $mail->is_active,
            'mailer' => $mail->mailer ?: 'smtp',
            'host' => $mail->host,
            'port' => $mail->port ?: 587,
            'username' => $mail->username,
            'password' => null,
            'encryption' => $mail->encryption ?: 'tls',
            'from_address' => $mail->from_address,
            'from_name' => $mail->from_name,
            'support_email' => $site?->support_email,
            'notification_emails' => $site?->notification_emails,
            'test_recipient' => $site?->support_email ?: $mail->from_address,
            'test_type' => 'contact',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin_trans.mail_smtp_section'))
                    ->description(__('admin_trans.mail_smtp_section_help'))
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('admin_trans.mail_use_dashboard_settings'))
                            ->helperText(__('admin_trans.mail_use_dashboard_settings_help'))
                            ->default(false),
                        Select::make('mailer')
                            ->label(__('admin_trans.mail_mailer'))
                            ->options([
                                'smtp' => 'SMTP',
                                'log' => 'Log',
                                'sendmail' => 'Sendmail',
                                'array' => 'Array',
                            ])
                            ->required()
                            ->live(),
                        TextInput::make('host')
                            ->label(__('admin_trans.mail_host'))
                            ->placeholder('smtp.gmail.com')
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),
                        TextInput::make('port')
                            ->label(__('admin_trans.mail_port'))
                            ->numeric()
                            ->default(587)
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),
                        TextInput::make('username')
                            ->label(__('admin_trans.mail_username'))
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),
                        TextInput::make('password')
                            ->label(__('admin_trans.mail_password'))
                            ->password()
                            ->revealable()
                            ->helperText(__('admin_trans.mail_password_help'))
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),
                        Select::make('encryption')
                            ->label(__('admin_trans.mail_encryption'))
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                'null' => __('admin_trans.mail_encryption_none'),
                            ])
                            ->visible(fn ($get) => $get('mailer') === 'smtp'),
                    ])
                    ->columns(2),
                Section::make(__('admin_trans.mail_from_section'))
                    ->schema([
                        TextInput::make('from_address')
                            ->label(__('admin_trans.mail_from_address'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('from_name')
                            ->label(__('admin_trans.mail_from_name'))
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make(__('admin_trans.notification_recipients'))
                    ->schema([
                        TextInput::make('support_email')
                            ->label(__('admin_trans.support_email'))
                            ->email()
                            ->maxLength(255),
                        Textarea::make('notification_emails')
                            ->label(__('admin_trans.notification_emails'))
                            ->rows(3)
                            ->helperText(__('admin_trans.notification_emails_help')),
                    ]),
                Section::make(__('admin_trans.send_test_email'))
                    ->schema([
                        TextInput::make('test_recipient')
                            ->label(__('admin_trans.test_recipient'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Select::make('test_type')
                            ->label(__('admin_trans.test_type'))
                            ->options([
                                'notification' => __('admin_trans.test_notification'),
                                'contact' => __('admin_trans.test_contact_form'),
                            ])
                            ->default('contact')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->columns(1)->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('mail-settings-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                        Action::make('sendTest')
                            ->label(__('admin_trans.send_test_email'))
                            ->color('gray')
                            ->action('sendTestEmail'),
                    ])->alignment(Alignment::Start),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $mail = MailSetting::currentOrNew();
        $mailPayload = [
            'is_active' => (bool) ($data['is_active'] ?? false),
            'mailer' => $data['mailer'] ?? 'smtp',
            'host' => $data['host'] ?? null,
            'port' => filled($data['port'] ?? null) ? (int) $data['port'] : null,
            'username' => $data['username'] ?? null,
            'encryption' => ($data['encryption'] ?? null) === 'null' ? null : ($data['encryption'] ?? null),
            'from_address' => $data['from_address'] ?? null,
            'from_name' => $data['from_name'] ?? null,
        ];

        if (filled($data['password'] ?? null)) {
            $mailPayload['password'] = $data['password'];
        }

        $mail->update($mailPayload);
        Cache::forget('mail_settings');
        app(MailConfigService::class)->apply($mail->fresh());

        $site = $this->getSiteSettingRecord();
        if ($site !== null) {
            $site->update(Arr::only($data, [
                'support_email',
                'notification_emails',
            ]));
            $this->forgetSiteLayoutCache($site);
        }

        Notification::make()
            ->success()
            ->title(__('admin_trans.success_saved'))
            ->send();
    }

    public function sendTestEmail(): void
    {
        $key = 'mail-settings-test:'.auth('admin')->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            Notification::make()
                ->danger()
                ->title(__('admin_trans.too_many_email_attempts', [
                    'seconds' => RateLimiter::availableIn($key),
                ]))
                ->send();

            return;
        }

        RateLimiter::hit($key, 60);

        $this->save();

        $data = $this->form->getState();
        $recipient = $data['test_recipient'] ?? null;
        $type = $data['test_type'] ?? 'contact';

        if (blank($recipient) || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->warning()
                ->title(__('admin_trans.no_recipient_configured'))
                ->send();

            return;
        }

        try {
            app(MailConfigService::class)->apply();
            app(FormNotificationService::class)->sendTestEmail($recipient, $type);

            Notification::make()
                ->success()
                ->title(__('admin_trans.test_email_sent', ['email' => $recipient]))
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('admin_trans.test_email_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getSiteSettingsFormElementId(): string
    {
        return 'mail-settings-form';
    }
}
