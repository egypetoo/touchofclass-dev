<?php

use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
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
        ->assertSee('Saturday to Thursday, 9:00 AM – 6:00 PM', false)
        ->assertSee('Friday off', false)
        ->assertSee('Choose what suits you', false)
        ->assertSee('The fastest way to reply and send photos of the space or the plan.', false)
        ->assertSee('output=embed', false)
        ->assertDontSee('6th of October, Giza, Egypt. Sunday to Thursday, 9:00 to 17:00.', false);
});
