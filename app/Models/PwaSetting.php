<?php

namespace App\Models;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PwaSetting extends Model
{
    protected $fillable = [
        'name_ar', 'name_en',
        'short_name_ar', 'short_name_en',
        'description_ar', 'description_en',
        'theme_color', 'background_color',
        'start_url', 'scope',
        'orientation', 'display',
        'lang', 'dir',
        'shortcuts', 'categories',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'shortcuts' => 'array',
            'categories' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('pwa_settings'));
        static::deleted(fn () => Cache::forget('pwa_settings'));
    }

    public static function active(): self
    {
        return Cache::remember('pwa_settings', 3600, function () {
            return static::query()->where('is_active', true)->first()
                ?? static::defaults();
        });
    }

    public static function defaults(): self
    {
        $site = SiteSetting::query()->first();
        $name = $site?->siteName() ?? config('app.name');

        return new static([
            'name_ar' => $site?->site_name_ar ?? $name,
            'name_en' => $site?->site_name_en ?? $name,
            'short_name_ar' => $site?->site_name_ar ?? $name,
            'short_name_en' => $site?->site_name_en ?? $name,
            'description_ar' => $site?->footer_body_ar,
            'description_en' => $site?->footer_body_en,
            'theme_color' => '#f59e0b',
            'background_color' => '#ffffff',
            'display' => 'standalone',
            'orientation' => 'portrait-primary',
            'lang' => 'ar',
            'dir' => 'rtl',
            'is_active' => true,
        ]);
    }
}
