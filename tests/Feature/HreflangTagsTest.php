<?php

use App\Models\Organization;
use App\Models\SiteSetting;
use App\Support\LocaleUrl;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->first();

    SiteSetting::query()->updateOrCreate(
        ['organization_id' => $organization->id],
        [
            'site_name_en' => 'United Sport',
            'site_name_ar' => 'يونايتد سبورت',
            'default_locale' => 'ar',
            'coming_soon_enabled' => false,
        ]
    );

    Cache::flush();
    config([
        'app.locale' => 'ar',
        'laravellocalization.useAcceptLanguageHeader' => false,
        'laravellocalization.hideDefaultLocaleInURL' => false,
    ]);
});

it('emits hreflang alternates with explicit arabic and english prefixes', function () {
    $html = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->getContent();

    preg_match_all('/hreflang="([^"]+)" href="([^"]+)"/', $html, $matches, PREG_SET_ORDER);

    $byLocale = collect($matches)->mapWithKeys(fn (array $m) => [$m[1] => $m[2]]);

    expect($byLocale)->toHaveKeys(['ar', 'en', 'x-default']);

    expect(parse_url($byLocale['ar'], PHP_URL_PATH) ?: '/')
        ->toStartWith('/ar')
        ->and(parse_url($byLocale['x-default'], PHP_URL_PATH) ?: '/')
        ->toStartWith('/ar')
        ->and(parse_url($byLocale['en'], PHP_URL_PATH) ?: '/')
        ->toStartWith('/en');

    expect($byLocale['ar'])->toBe($byLocale['x-default']);
});

it('builds locale switch urls with the arabic prefix', function () {
    config([
        'app.locale' => 'ar',
        'laravellocalization.hideDefaultLocaleInURL' => false,
    ]);

    expect(parse_url(LocaleUrl::toLocalePath('ar'), PHP_URL_PATH) ?: '/')
        ->toStartWith('/ar')
        ->and(parse_url(LocaleUrl::toLocalePath('en'), PHP_URL_PATH) ?: '/')
        ->toStartWith('/en')
        ->and(parse_url(LocaleUrl::toLocalePath('ar', '/about-us'), PHP_URL_PATH) ?: '/')
        ->toBe('/ar/about-us');
});
