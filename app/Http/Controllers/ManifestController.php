<?php

namespace App\Http\Controllers;

use App\Models\PwaSetting;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $locale = app()->getLocale();
        $pwa = PwaSetting::active();
        $site = SiteSetting::query()->first();

        $name = $locale === 'ar'
            ? ($pwa->name_ar ?? $site?->site_name_ar ?? config('app.name'))
            : ($pwa->name_en ?? $site?->site_name_en ?? config('app.name'));

        $shortName = $locale === 'ar'
            ? ($pwa->short_name_ar ?? $name)
            : ($pwa->short_name_en ?? $name);

        $description = $locale === 'ar'
            ? ($pwa->description_ar ?? '')
            : ($pwa->description_en ?? '');

        $logo = $site?->logoUrl() ?? asset('assets/images/favicon.ico');

        $manifest = [
            'name' => $name,
            'short_name' => $shortName,
            'description' => strip_tags((string) $description),
            'start_url' => $pwa->start_url ?: url('/'.$locale),
            'scope' => $pwa->scope ?: url('/'),
            'display' => $pwa->display ?? 'standalone',
            'background_color' => $pwa->background_color ?? '#ffffff',
            'theme_color' => $pwa->theme_color ?? '#f59e0b',
            'orientation' => $pwa->orientation ?? 'portrait-primary',
            'lang' => $pwa->lang ?? $locale,
            'dir' => $pwa->dir ?? ($locale === 'ar' ? 'rtl' : 'ltr'),
            'icons' => [
                ['src' => $logo, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => $logo, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
            ],
            'categories' => $pwa->categories ?? ['business'],
            'shortcuts' => $pwa->shortcuts ?? [
                ['name' => __('site.home'), 'url' => url('/'.$locale), 'icons' => [['src' => $logo, 'sizes' => '96x96']]],
                ['name' => __('site.contact_us'), 'url' => url('/'.$locale.'/contact-us'), 'icons' => [['src' => $logo, 'sizes' => '96x96']]],
            ],
        ];

        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json');
    }
}
