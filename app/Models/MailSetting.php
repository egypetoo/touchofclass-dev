<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MailSetting extends Model
{
    protected $fillable = [
        'is_active',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'port' => 'integer',
            'password' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('mail_settings'));
        static::deleted(fn () => Cache::forget('mail_settings'));
    }

    public static function current(): ?self
    {
        return Cache::remember('mail_settings', now()->addMinutes(30), function () {
            return static::query()->first();
        });
    }

    public static function currentOrNew(): self
    {
        return static::query()->firstOrCreate([], [
            'is_active' => false,
            'mailer' => config('mail.default', 'smtp'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => (int) config('mail.mailers.smtp.port', 587),
            'username' => config('mail.mailers.smtp.username'),
            'encryption' => config('mail.mailers.smtp.scheme') ?: 'tls',
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ]);
    }
}
