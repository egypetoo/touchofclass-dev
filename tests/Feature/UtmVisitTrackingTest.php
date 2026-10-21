<?php

use App\Filament\Pages\ManageUtmVisitsReport;
use App\Models\Admin;
use App\Models\ContactMessage;
use App\Models\Organization;
use App\Models\SiteSetting;
use App\Models\UtmVisit;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Models\Role;

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

it('records a utm visit when a campaign landing is opened', function () {
    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/contact-us?utm_source=snapchat&utm_campaign=launch')
        ->assertOk();

    expect(UtmVisit::query()->count())->toBe(1);

    $visit = UtmVisit::query()->first();

    expect($visit->utm_source)->toBe('snapchat')
        ->and($visit->utm_campaign)->toBe('launch')
        ->and($visit->path)->toContain('contact-us');
});

it('does not duplicate the same campaign visit inside one session', function () {
    $middleware = [
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ];

    $this->withoutMiddleware($middleware)->get('/contact-us?utm_source=facebook')->assertOk();
    $this->withoutMiddleware($middleware)->get('/contact-us?utm_source=facebook')->assertOk();

    expect(UtmVisit::query()->count())->toBe(1);
});

it('records a new visit when the platform changes', function () {
    $middleware = [
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ];

    $this->withoutMiddleware($middleware)->get('/?utm_source=facebook')->assertOk();
    $this->withoutMiddleware($middleware)->get('/?utm_source=instagram')->assertOk();

    expect(UtmVisit::query()->count())->toBe(2)
        ->and(UtmVisit::query()->pluck('utm_source')->all())->toContain('facebook', 'instagram');
});

it('lets an admin open the utm visits report', function () {
    UtmVisit::query()->create([
        'utm_source' => 'snapchat',
        'utm_medium' => 'social',
        'path' => '/contact-us',
        'landing_page' => 'https://example.test/contact-us?utm_source=snapchat',
    ]);

    ContactMessage::query()->create([
        'name' => 'Lead',
        'email' => 'lead@example.com',
        'phone' => '0100',
        'message' => 'Hi',
        'utm_source' => 'snapchat',
    ]);

    $admin = Admin::factory()->create();
    $admin->assignRole(Role::findOrCreate('super_admin', 'admin'));

    $this->actingAs($admin, 'admin')
        ->get(ManageUtmVisitsReport::getUrl())
        ->assertOk()
        ->assertSee(__('admin_trans.utm_visits_report'), false)
        ->assertSee(__('admin_trans.utm_date_range'), false)
        ->assertSee('Snapchat', false);
});
