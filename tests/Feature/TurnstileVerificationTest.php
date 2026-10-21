<?php

use App\Models\ContactMessage;
use App\Models\Organization;
use App\Models\SiteSetting;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->firstOrFail();

    SiteSetting::query()->updateOrCreate(
        ['organization_id' => $organization->id],
        [
            'site_name_en' => 'Atlantis Trade',
            'site_name_ar' => 'أطلانتس تريد',
            'turnstile_enabled' => false,
            'turnstile_site_key' => null,
            'turnstile_secret_key' => null,
        ]
    );

    Cache::flush();
});

function enableTurnstile(): SiteSetting
{
    $setting = SiteSetting::query()->firstOrFail();

    $setting->update([
        'turnstile_enabled' => true,
        'turnstile_site_key' => '1x00000000000000000000AA',
        'turnstile_secret_key' => '1x0000000000000000000000000000000AA',
    ]);

    Cache::flush();

    return $setting->fresh();
}

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ahmed Tester',
        'email' => 'ahmed@example.com',
        'phone' => '+201000000000',
        'message' => 'Hello from Turnstile test',
    ], $overrides);
}

it('accepts contact submissions when turnstile is disabled', function () {
    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->postJson(route('contact.store'), contactPayload());

    $response->assertOk()
        ->assertJsonPath('message', __('site.contact_message_sent'));

    expect(ContactMessage::query()->count())->toBe(1);
});

it('requires the turnstile token when enabled', function () {
    enableTurnstile();

    Http::fake();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->postJson(route('contact.store'), contactPayload());

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['cf-turnstile-response']);

    expect(ContactMessage::query()->count())->toBe(0);
    Http::assertNothingSent();
});

it('rejects an invalid turnstile token', function () {
    enableTurnstile();

    Http::preventStrayRequests();
    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ]),
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->postJson(route('contact.store'), contactPayload([
            'cf-turnstile-response' => 'bad-token',
        ]));

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['cf-turnstile-response']);

    expect(ContactMessage::query()->count())->toBe(0);
});

it('accepts a valid turnstile token', function () {
    enableTurnstile();

    Http::preventStrayRequests();
    Http::fake([
        'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
            'success' => true,
        ]),
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->postJson(route('contact.store'), contactPayload([
            'cf-turnstile-response' => 'valid-token',
        ]));

    $response->assertOk()
        ->assertJsonPath('message', __('site.contact_message_sent'));

    expect(ContactMessage::query()->count())->toBe(1);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['response'] === 'valid-token'
            && $request['secret'] === '1x0000000000000000000000000000000AA';
    });
});

it('shows the turnstile widget on the contact page when enabled', function () {
    enableTurnstile();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/contact-us');

    $response->assertOk()
        ->assertSee('cf-turnstile', false)
        ->assertSee('1x00000000000000000000AA', false)
        ->assertSee('challenges.cloudflare.com/turnstile/v0/api.js', false);
});
