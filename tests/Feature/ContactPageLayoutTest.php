<?php

use App\Enums\BranchTypeEnum;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\SiteSetting;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('renders the King Motors contact page with the design form and map', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/contact-us');

    $response->assertOk()
        ->assertSee('km-cform-card', false)
        ->assertSee('info@touchofclass.com', false)
        ->assertSee('id="contact-form"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSee('name="service_id"', false)
        ->assertSee('Interior Design', false)
        ->assertSee('Cairo, Arab Republic of Egypt', false)
        ->assertSee('tel:+201112302002', false)
        ->assertSee('+20 1112302002', false)
        ->assertSee('Saturday to Thursday, 9:00 AM – 6:00 PM', false)
        ->assertSee('Friday off', false)
        ->assertSee('Choose what suits you', false)
        ->assertSee('The fastest way to reply and send photos of the space or the plan.', false)
        ->assertSee('output=embed', false)
        ->assertDontSee('6th of October, Giza, Egypt. Sunday to Thursday, 9:00 to 17:00.', false);
});

it('lists every dashboard phone and every branch address', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $organizationId = Organization::query()->value('id');

    SiteSetting::query()->where('organization_id', $organizationId)->update([
        'cta_phone' => [
            ['code' => '20', 'number' => '1112302002'],
            ['code' => '20', 'number' => '1000000002'],
        ],
    ]);

    Branch::query()->create([
        'organization_id' => $organizationId,
        'type' => BranchTypeEnum::Other,
        'name' => ['en' => 'October studio', 'ar' => 'استوديو أكتوبر'],
        'address' => ['en' => '6th of October, Giza', 'ar' => 'السادس من أكتوبر، الجيزة'],
        'is_active' => true,
        'order' => 2,
    ]);

    Cache::flush();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/contact-us');

    $response->assertOk()
        ->assertSee('tel:+201112302002', false)
        ->assertSee('tel:+201000000002', false)
        ->assertSee('October studio', false)
        ->assertSee('6th of October, Giza', false)
        ->assertSee('Cairo, Arab Republic of Egypt', false);

    $html = $response->getContent();

    expect(substr_count($html, 'tel:+201000000002'))->toBeGreaterThanOrEqual(2)
        ->and(substr_count($html, '6th of October, Giza'))->toBeGreaterThanOrEqual(2);
});
