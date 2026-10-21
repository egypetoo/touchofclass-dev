<?php

namespace App\Services\Mail;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;

class MailConfigService
{
    public function apply(?MailSetting $settings = null): void
    {
        $settings ??= MailSetting::current();

        if ($settings === null || ! $settings->is_active) {
            return;
        }

        $mailer = filled($settings->mailer) ? $settings->mailer : 'smtp';

        Config::set('mail.default', $mailer);

        if (filled($settings->from_address)) {
            Config::set('mail.from.address', $settings->from_address);
        }

        if (filled($settings->from_name)) {
            Config::set('mail.from.name', $settings->from_name);
        }

        if ($mailer !== 'smtp') {
            return;
        }

        Config::set('mail.mailers.smtp.transport', 'smtp');

        if (filled($settings->host)) {
            Config::set('mail.mailers.smtp.host', $settings->host);
        }

        if (filled($settings->port)) {
            Config::set('mail.mailers.smtp.port', (int) $settings->port);
        }

        if ($settings->username !== null) {
            Config::set('mail.mailers.smtp.username', $settings->username);
        }

        if (filled($settings->password)) {
            Config::set('mail.mailers.smtp.password', $settings->password);
        }

        $encryption = $settings->encryption;
        if ($encryption === 'null' || $encryption === '') {
            $encryption = null;
        }

        Config::set('mail.mailers.smtp.scheme', $encryption);
    }
}
