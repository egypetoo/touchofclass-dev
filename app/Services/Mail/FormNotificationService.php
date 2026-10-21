<?php

namespace App\Services\Mail;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FormNotificationService
{
    /**
     * @return array<int, string>
     */
    public function recipientEmails(): array
    {
        $setting = SiteSetting::query()->first();
        if ($setting === null) {
            return [];
        }

        $emails = [];

        if (filled($setting->support_email) && filter_var($setting->support_email, FILTER_VALIDATE_EMAIL)) {
            $emails[] = $setting->support_email;
        }

        if (filled($setting->notification_emails)) {
            foreach (preg_split('/[\n,]+/', $setting->notification_emails) as $email) {
                $email = trim($email);
                if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $email;
                }
            }
        }

        return array_values(array_unique($emails));
    }

    public function siteName(): string
    {
        $setting = SiteSetting::query()->first();

        return $setting?->siteName() ?: config('app.name');
    }

    public function sendContactNotification(array $data): bool
    {
        $recipients = $this->recipientEmails();
        if ($recipients === []) {
            return false;
        }

        $appName = $this->siteName();
        $subject = __('admin_trans.contact_form_email_subject', ['site' => $appName]);
        $html = $this->buildContactHtml($appName, $data);

        return $this->sendHtml($recipients, $subject, $html);
    }

    public function sendTestEmail(string $recipient, string $type): void
    {
        app(MailConfigService::class)->apply();

        $appName = $this->siteName();

        if ($type === 'contact') {
            $subject = __('admin_trans.test_contact_form').' - '.$appName;
            $html = $this->buildContactTestHtml($appName);
        } else {
            $subject = __('admin_trans.test_notification').' - '.$appName;
            $html = $this->buildNotificationTestHtml($appName);
        }

        Mail::html($html, fn ($mail) => $mail->to($recipient)->subject($subject));
    }

    /**
     * @param  array<int, string>  $recipients
     */
    protected function sendHtml(array $recipients, string $subject, string $html): bool
    {
        try {
            app(MailConfigService::class)->apply();

            foreach ($recipients as $recipient) {
                Mail::html($html, fn ($mail) => $mail->to($recipient)->subject($subject));
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Form notification email failed: '.$e->getMessage());

            return false;
        }
    }

    protected function buildContactHtml(string $appName, array $data): string
    {
        $rows = '';
        foreach ($data as $label => $value) {
            if (blank($value)) {
                continue;
            }
            $rows .= '<tr><td style="padding:8px;border:1px solid #eee;font-weight:600;">'.e($label).'</td>';
            $rows .= '<td style="padding:8px;border:1px solid #eee;">'.e((string) $value).'</td></tr>';
        }

        return "<!DOCTYPE html><html><body style='font-family:Arial,sans-serif;'>
            <h2>".e(__('admin_trans.new_contact_message'))." — {$appName}</h2>
            <table style='border-collapse:collapse;width:100%;max-width:600px;'>{$rows}</table>
        </body></html>";
    }

    protected function buildNotificationTestHtml(string $appName): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');

        return "<!DOCTYPE html><html><body style='font-family:Arial,sans-serif;padding:24px;'>
            <h2>".e(__('admin_trans.test_notification'))."</h2>
            <p>".e(__('admin_trans.test_email_success_body'))."</p>
            <p><strong>{$appName}</strong><br>{$timestamp}</p>
        </body></html>";
    }

    protected function buildContactTestHtml(string $appName): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');

        return "<!DOCTYPE html><html><body style='font-family:Arial,sans-serif;padding:24px;'>
            <h2>".e(__('admin_trans.test_contact_form'))."</h2>
            <p>".e(__('admin_trans.test_contact_email_success_body'))."</p>
            <p><strong>{$appName}</strong><br>{$timestamp}</p>
        </body></html>";
    }
}
