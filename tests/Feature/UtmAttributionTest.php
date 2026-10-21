<?php

use App\Models\ContactMessage;
use App\Models\Organization;
use App\Models\SiteSetting;
use App\Services\Marketing\UtmAttribution;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
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
        ]
    );

    Cache::flush();
});

it('stores utm parameters in the session on landing', function () {
    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/?utm_source=facebook&utm_medium=cpc&utm_campaign=spring_sale')
        ->assertOk();

    expect(session(UtmAttribution::SESSION_KEY))
        ->toMatchArray([
            'utm_source' => 'facebook',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring_sale',
        ])
        ->toHaveKey('landing_page');
});

it('keeps the latest campaign touch when a new utm arrives', function () {
    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/?utm_source=facebook&utm_campaign=first')
        ->assertOk();

    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/?utm_source=instagram&utm_campaign=second')
        ->assertOk();

    expect(session(UtmAttribution::SESSION_KEY))
        ->toMatchArray([
            'utm_source' => 'instagram',
            'utm_campaign' => 'second',
        ]);
});

it('attaches the platform utm data to contact submissions', function () {
    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/?utm_source=tiktok&utm_medium=social&utm_campaign=launch')
        ->assertOk();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->postJson(route('contact.store'), [
            'name' => 'Ahmed Tester',
            'email' => 'ahmed@example.com',
            'phone' => '+201000000000',
            'message' => 'Need a quote',
        ]);

    $response->assertOk();

    $message = ContactMessage::query()->first();

    expect($message)->not->toBeNull()
        ->and($message->utm_source)->toBe('tiktok')
        ->and($message->utm_medium)->toBe('social')
        ->and($message->utm_campaign)->toBe('launch')
        ->and($message->landing_page)->not->toBeEmpty();
});

it('captures snapchat from the contact-us page like campaign links', function () {
    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/contact-us?utm_source=snapchat')
        ->assertOk()
        ->assertSee('name="utm_source"', false)
        ->assertSee('value="snapchat"', false);

    expect(session(UtmAttribution::SESSION_KEY)['utm_source'] ?? null)->toBe('snapchat');

    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->postJson(route('contact.store'), [
            'name' => 'Snap Lead',
            'email' => 'snap@example.com',
            'phone' => '+201111111111',
            'message' => 'From Snapchat ad',
        ])
        ->assertOk();

    expect(ContactMessage::query()->latest('id')->first())
        ->utm_source->toBe('snapchat');
});

it('labels common advertising platforms', function () {
    $utm = app(UtmAttribution::class);

    expect($utm->platformLabel('facebook'))->toBe('Facebook')
        ->and($utm->platformLabel('instagram'))->toBe('Instagram')
        ->and($utm->platformLabel('googleads'))->toBe('Google')
        ->and($utm->platformLabel('tiktok'))->toBe('TikTok')
        ->and($utm->platformLabel('snapchat'))->toBe('Snapchat');
});
