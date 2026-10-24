<?php

use App\Models\MailSetting;
use App\Services\Mail\MailConfigService;

it('maps dashboard ssl encryption to the smtps mail scheme', function () {
    $settings = MailSetting::query()->create([
        'is_active' => true,
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 465,
        'encryption' => 'ssl',
        'from_address' => 'info@touchofclass-eg.com',
        'from_name' => 'Touch of Class',
    ]);

    app(MailConfigService::class)->apply($settings);

    expect(config('mail.mailers.smtp.scheme'))->toBe('smtps')
        ->and(config('mail.mailers.smtp.host'))->toBe('smtp.example.com')
        ->and(config('mail.mailers.smtp.port'))->toBe(465);
});

it('maps dashboard tls encryption to the smtp mail scheme', function () {
    $settings = MailSetting::query()->create([
        'is_active' => true,
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 587,
        'encryption' => 'tls',
        'from_address' => 'info@touchofclass-eg.com',
        'from_name' => 'Touch of Class',
    ]);

    app(MailConfigService::class)->apply($settings);

    expect(config('mail.mailers.smtp.scheme'))->toBe('smtp');
});
