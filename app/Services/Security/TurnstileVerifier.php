<?php

namespace App\Services\Security;

use App\Models\SiteSetting;
use App\Services\SiteLayoutDataService;
use Illuminate\Support\Facades\Http;

class TurnstileVerifier
{
    public function __construct(
        private ?SiteSetting $siteSetting = null,
    ) {
        $this->siteSetting ??= app(SiteLayoutDataService::class)->siteSetting();
    }

    public function isEnabled(): bool
    {
        return (bool) ($this->siteSetting?->turnstile_enabled)
            && filled($this->siteSetting?->turnstile_site_key)
            && filled($this->siteSetting?->turnstile_secret_key);
    }

    public function siteKey(): ?string
    {
        return $this->isEnabled() ? $this->siteSetting?->turnstile_site_key : null;
    }

    public function verify(?string $response, ?string $ip = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (blank($response)) {
            return false;
        }

        $result = Http::asForm()
            ->connectTimeout(3)
            ->timeout(10)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $this->siteSetting->turnstile_secret_key,
                'response' => $response,
                'remoteip' => $ip,
            ])
            ->json();

        return (bool) ($result['success'] ?? false);
    }
}
