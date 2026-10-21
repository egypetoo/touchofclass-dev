<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ForceComingSoonMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request) || ! $this->isComingSoonEnabled()) {
            return $next($request);
        }

        $siteSetting = Cache::remember('site_settings_coming_soon', now()->addMinutes(5), function () {
            return SiteSetting::query()->orderBy('id')->first();
        });

        return response()->view('site.coming-soon', [
            'siteSetting' => $siteSetting,
        ]);
    }

    protected function shouldBypass(Request $request): bool
    {
        if (Auth::guard('admin')->check()) {
            return true;
        }

        $path = trim($request->path(), '/');

        if (preg_match('#^livewire(-[A-Za-z0-9]+)?(/|$)#', $path) === 1) {
            return true;
        }

        $excludedPrefixes = [
            'admin',
            'livewire',
            'filament',
            'up',
            'api',
            'sanctum',
            'storage',
            'vendor',
            'build',
            'sw.js',
            'manifest.json',
            'robots.txt',
            'sitemap.xml',
        ];

        foreach ($excludedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    protected function isComingSoonEnabled(): bool
    {
        return (bool) Cache::remember('site_coming_soon_enabled', now()->addMinutes(5), function () {
            return SiteSetting::query()->orderBy('id')->value('coming_soon_enabled');
        });
    }
}
