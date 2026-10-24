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
        $subject = 'New request from '.$appName;
        $html = $this->contactMessageHtml($appName, $data);

        return $this->sendHtml($recipients, $subject, $html);
    }

    public function sendTestEmail(string $recipient, string $type): void
    {
        app(MailConfigService::class)->apply();

        $appName = $this->siteName();

        if ($type === 'contact') {
            $subject = 'New request from '.$appName;
            $html = $this->contactMessageHtml($appName, [
                'Request type' => 'Interior design',
                'Name' => 'Sarah Samir',
                'Phone' => '01112302002',
                'Email' => 'info@touchofclass-eg.com',
                'Message' => 'I would like to book a consultation for a residential project.',
            ]);
        } else {
            $subject = 'Test notification - '.$appName;
            $html = $this->contactMessageHtml($appName, [
                'Message' => 'If you received this message, notification emails are working.',
            ]);
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

    /**
     * @param  array<string, mixed>  $data
     */
    public function contactMessageHtml(string $appName, array $data): string
    {
        $rows = [];

        foreach ($data as $label => $value) {
            if (blank($value)) {
                continue;
            }

            $rows[] = [
                'icon' => $this->rowIcon((string) $label),
                'label' => (string) $label,
                'value' => (string) $value,
            ];
        }

        return view('mail.contact-notification', [
            'title' => 'New request from '.$appName,
            'rows' => $rows,
        ])->render();
    }

    private function rowIcon(string $label): string
    {
        return match ($label) {
            'Name' => '👤',
            'Phone' => '📱',
            'Email' => '✉️',
            'Request type' => '📋',
            'Message' => '💬',
            'Source' => '🔗',
            'Campaign' => '📣',
            default => '📋',
        };
    }
}
