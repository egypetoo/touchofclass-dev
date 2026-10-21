<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'site_name_en',
        'site_name_ar',
        'logo_en_path',
        'logo_ar_path',
        'favicon_path',
        'default_locale',
        'cta_phone',
        'cta_whatsapp',
        'cta_label_en',
        'cta_label_ar',
        'support_email',
        'notification_emails',
        'footer_copyright_en',
        'footer_copyright_ar',
        'footer_body_en',
        'footer_body_ar',
        'extra',
        'turnstile_enabled',
        'turnstile_site_key',
        'turnstile_secret_key',
        'coming_soon_enabled',
        'coming_soon_title_en',
        'coming_soon_title_ar',
        'coming_soon_message_en',
        'coming_soon_message_ar',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'cta_phone' => 'array',
            'cta_whatsapp' => 'array',
            'turnstile_enabled' => 'boolean',
            'coming_soon_enabled' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SiteSocialLink::class)->orderBy('sort_order');
    }

    /**
     * Public display name for a locale, falling back to organization names.
     */
    public function siteName(): string
    {
        $name = $this->{'site_name_'.app()->getLocale()};
        if (filled($name)) {
            return (string) $name;
        }

        $fallback = app()->getLocale() === 'ar'
            ? $this->site_name_en
            : $this->site_name_ar;

        if (filled($fallback)) {
            return (string) $fallback;
        }

        return (string) ($this->organization?->name_en ?: config('app.name'));
    }

    public function logoUrl(): string
    {
        return $this->publicStorageUrl($this->preferredLogoPath())
            ?? asset('assets/images/logo/logo-transparent.webp');
    }

    public function preferredLogoPath(): ?string
    {
        $localePath = $this->{'logo_'.app()->getLocale().'_path'};
        $fallbackPath = app()->getLocale() === 'ar'
            ? $this->logo_en_path
            : $this->logo_ar_path;
        $path = filled($localePath) ? $localePath : $fallbackPath;

        return filled($path) ? (string) $path : null;
    }

    /**
     * Web path for a file on the public disk. Relative so it works when APP_URL differs from the browser host.
     */
    public function publicStorageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return '/storage/'.$path;
    }

    public function faviconUrl(): string
    {
        if (filled($this->favicon_path)) {
            return $this->publicStorageUrl($this->favicon_path)
                ?? asset('assets/images/logo/logo-transparent.webp');
        }

        return asset('assets/images/logo/logo-transparent.webp');
    }

    public function fax(): ?string
    {
        $fax = $this->extra['fax'] ?? null;

        return filled($fax) ? (string) $fax : null;
    }

    public function footerBody(): ?string
    {
        return $this->{'footer_body_'.app()->getLocale()};
    }

    public function footerCopyright(): string
    {
        $siteName = $this->siteName() ?: 'Mina Pack';

        return '© '.date('Y').' All Rights Reserved '.$siteName.', Developed and Designed by Be Group';
    }

    public function ctaLabel(): ?string
    {
        $label = $this->{'cta_label_'.app()->getLocale()};

        return filled($label) ? $label : $this->cta_label_en;
    }

    public function getCtaLabelAttribute(): ?string
    {
        return $this->ctaLabel();
    }

    /**
     * @return list<array{code: string, number: string, display: string, digits: string, tel: string}>
     */
    public function ctaPhoneEntries(): array
    {
        return $this->normalizeContactEntries($this->cta_phone);
    }

    /**
     * @return list<array{code: string, number: string, display: string, digits: string, tel: string, url: string|null}>
     */
    public function ctaWhatsappEntries(): array
    {
        return array_map(function (array $entry) {
            $entry['url'] = filled($entry['digits']) ? 'https://wa.me/'.$entry['digits'] : null;

            return $entry;
        }, $this->normalizeContactEntries($this->cta_whatsapp));
    }

    /**
     * @return list<string>
     */
    public function ctaPhones(): array
    {
        return array_column($this->ctaPhoneEntries(), 'display');
    }

    /**
     * @return list<string>
     */
    public function ctaWhatsappNumbers(): array
    {
        return array_column($this->ctaWhatsappEntries(), 'display');
    }

    public function primaryCtaPhone(): ?string
    {
        return $this->ctaPhoneEntries()[0]['display'] ?? null;
    }

    public function primaryCtaPhoneTel(): ?string
    {
        return $this->ctaPhoneEntries()[0]['tel'] ?? null;
    }

    public function primaryCtaWhatsapp(): ?string
    {
        return $this->ctaWhatsappEntries()[0]['display'] ?? null;
    }

    public function whatsappUrl(?string $number = null): ?string
    {
        if ($number !== null) {
            $digits = preg_replace('/\D+/', '', $number);

            return filled($digits) ? 'https://wa.me/'.$digits : null;
        }

        return $this->ctaWhatsappEntries()[0]['url'] ?? null;
    }

    /**
     * @return list<string>
     */
    public function whatsappUrls(): array
    {
        return array_values(array_filter(array_column($this->ctaWhatsappEntries(), 'url')));
    }

    public function isTurnstileEnabled(): bool
    {
        return (bool) $this->turnstile_enabled
            && filled($this->turnstile_site_key)
            && filled($this->turnstile_secret_key);
    }

    public function isComingSoonEnabled(): bool
    {
        return (bool) $this->coming_soon_enabled;
    }

    public function comingSoonTitle(): string
    {
        $title = $this->{'coming_soon_title_'.app()->getLocale()};
        if (filled($title)) {
            return (string) $title;
        }

        $fallback = app()->getLocale() === 'ar'
            ? $this->coming_soon_title_en
            : $this->coming_soon_title_ar;

        if (filled($fallback)) {
            return (string) $fallback;
        }

        return (string) __('site.coming_soon_default_title');
    }

    public function comingSoonMessage(): string
    {
        $message = $this->{'coming_soon_message_'.app()->getLocale()};
        if (filled($message)) {
            return (string) $message;
        }

        $fallback = app()->getLocale() === 'ar'
            ? $this->coming_soon_message_en
            : $this->coming_soon_message_ar;

        if (filled($fallback)) {
            return (string) $fallback;
        }

        return (string) __('site.coming_soon_default_message');
    }

    /**
     * @return list<array{code: string, number: string, display: string, digits: string, tel: string}>
     */
    protected function normalizeContactEntries(mixed $value): array
    {
        if (blank($value)) {
            return [];
        }

        $items = is_string($value)
            ? (preg_split('/[\n,]+/', $value) ?: [])
            : (is_array($value) ? $value : []);

        return collect($items)
            ->map(fn ($item) => $this->normalizeContactEntry($item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array{code: string, number: string, display: string, digits: string, tel: string}|null
     */
    protected function normalizeContactEntry(mixed $item): ?array
    {
        $code = '';
        $number = '';

        if (is_array($item)) {
            $code = preg_replace('/\D+/', '', (string) ($item['code'] ?? '')) ?? '';
            $number = trim((string) ($item['number'] ?? $item['value'] ?? ''));
            if ($number === '' && isset($item[0])) {
                $number = trim((string) $item[0]);
            }
        } else {
            $number = trim((string) $item);
        }

        if ($number === '' && $code === '') {
            return null;
        }

        $numberDigits = preg_replace('/\D+/', '', $number) ?? '';

        // Local numbers often start with 0; strip it before joining with country code.
        $localDigits = ltrim($numberDigits, '0');
        if ($localDigits === '' && $numberDigits !== '') {
            $localDigits = $numberDigits;
        }

        $digits = $code.$localDigits;

        if ($digits === '') {
            return null;
        }

        $displayNumber = $localDigits !== '' ? $localDigits : $number;
        $display = $code !== ''
            ? '+'.$code.' '.$displayNumber
            : $displayNumber;

        $tel = $code !== '' ? '+'.$code.$localDigits : ($localDigits !== '' ? $localDigits : $numberDigits);

        return [
            'code' => $code,
            'number' => $displayNumber,
            'display' => trim($display),
            'digits' => $digits,
            'tel' => $tel,
        ];
    }
}
