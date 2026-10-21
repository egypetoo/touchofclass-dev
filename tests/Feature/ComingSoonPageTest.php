<?php

use App\Models\Organization;
use App\Models\SiteSetting;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->first();

    SiteSetting::query()->firstOrCreate(
        ['organization_id' => $organization->id],
        [
            'site_name_en' => 'United Sport',
            'site_name_ar' => 'يونايتد سبورت',
            'coming_soon_enabled' => false,
        ]
    );

    Cache::flush();
});

it('shows the website when coming soon is disabled', function () {
    SiteSetting::query()->first()?->update(['coming_soon_enabled' => false]);
    Cache::flush();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/');

    $response->assertOk()
        ->assertDontSee('coming-soon-page', false);
});

it('shows the coming soon page when enabled', function () {
    SiteSetting::query()->first()?->update([
        'coming_soon_enabled' => true,
        'coming_soon_title_en' => 'Launching Soon',
        'coming_soon_message_en' => 'Site content is being prepared.',
    ]);
    Cache::flush();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/');

    $response->assertOk()
        ->assertSee('coming-soon-page', false)
        ->assertSee('Launching Soon', false)
        ->assertSee('Site content is being prepared.', false);
});

it('keeps livewire assets reachable while coming soon is enabled', function () {
    SiteSetting::query()->first()?->update(['coming_soon_enabled' => true]);
    Cache::flush();

    $livewireScriptUri = collect(Route::getRoutes()->getRoutes())
        ->map(fn ($route) => $route->uri())
        ->first(fn (string $uri) => str_starts_with($uri, 'livewire') && str_ends_with($uri, '/livewire.js'));

    expect($livewireScriptUri)->not->toBeNull();

    $this->get('/'.$livewireScriptUri)
        ->assertOk()
        ->assertDontSee('coming-soon-page', false);
});

it('keeps the admin panel reachable while coming soon is enabled', function () {
    SiteSetting::query()->first()?->update(['coming_soon_enabled' => true]);
    Cache::flush();

    $response = $this->get('/admin/login');

    $response->assertOk()
        ->assertDontSee('coming-soon-page', false);
});
