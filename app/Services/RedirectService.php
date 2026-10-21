<?php

namespace App\Services;

use App\Models\Redirect;
use Illuminate\Support\Facades\Cache;

class RedirectService
{
    public function create(array $data): Redirect
    {
        $redirect = Redirect::query()->create($data);
        $this->forgetCache();

        return $redirect;
    }

    public function update(Redirect $redirect, array $data): Redirect
    {
        $redirect->update($data);
        $this->forgetCache();

        return $redirect;
    }

    public function delete(Redirect $redirect): void
    {
        $redirect->delete();
        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        Cache::forget('redirects.map');
    }

    public static function isSafeRedirectTarget(string $target): bool
    {
        $target = trim($target);

        if ($target === '') {
            return true;
        }

        if (str_starts_with($target, '/')) {
            return true;
        }

        if (! filter_var($target, FILTER_VALIDATE_URL)) {
            return false;
        }

        $targetParts = parse_url($target);
        $appParts = parse_url((string) config('app.url'));

        if (! isset($targetParts['scheme'], $targetParts['host'])) {
            return false;
        }

        $scheme = strtolower((string) $targetParts['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $targetHost = strtolower((string) ($targetParts['host'] ?? ''));
        $appHost = strtolower((string) ($appParts['host'] ?? ''));

        return $targetHost !== '' && $appHost !== '' && $targetHost === $appHost;
    }
}
