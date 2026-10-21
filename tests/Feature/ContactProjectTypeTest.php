<?php

use App\Models\ContactMessage;
use App\Models\Organization;
use App\Models\SiteSetting;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    SiteSetting::query()->updateOrCreate(
        ['organization_id' => Organization::query()->firstOrFail()->id],
        ['turnstile_enabled' => false]
    );

    $this->withoutMiddleware([
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ]);
});

it('shows the design contact form fields', function () {
    $this->get('/contact-us')
        ->assertOk()
        ->assertSee('id="contact-form"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSee('name="message"', false);
});

it('stores the company name at the top of the message', function () {
    $this->postJson(route('contact.store'), [
        'name' => 'Ahmed Tester',
        'company' => 'Acme Foods',
        'email' => 'ahmed@example.com',
        'phone' => '+201000000000',
        'message' => 'Need 10,000 printed cartons',
    ])->assertOk();

    expect(ContactMessage::query()->sole()->message)
        ->toBe(__('site.company').": Acme Foods\nNeed 10,000 printed cartons");
});

it('rejects a company name longer than 255 characters', function () {
    $this->postJson(route('contact.store'), [
        'name' => 'Ahmed Tester',
        'email' => 'ahmed@example.com',
        'phone' => '+201000000000',
        'company' => str_repeat('a', 256),
    ])->assertJsonValidationErrors('company');

    expect(ContactMessage::query()->count())->toBe(0);
});

it('stores the project type the customer typed', function () {
    $this->postJson(route('contact.store'), [
        'name' => 'Ahmed Tester',
        'email' => 'ahmed@example.com',
        'phone' => '+201000000000',
        'project_type' => 'Hotel restaurant equipment',
        'message' => 'Need a quote',
    ])->assertOk();

    expect(ContactMessage::query()->sole()->project_type)->toBe('Hotel restaurant equipment');
});

it('rejects a project type longer than 255 characters', function () {
    $this->postJson(route('contact.store'), [
        'name' => 'Ahmed Tester',
        'email' => 'ahmed@example.com',
        'phone' => '+201000000000',
        'project_type' => str_repeat('a', 256),
    ])->assertJsonValidationErrors('project_type');

    expect(ContactMessage::query()->count())->toBe(0);
});
