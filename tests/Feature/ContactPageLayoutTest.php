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
        ->assertSee('info@kingmotors.com', false)
        ->assertSee('id="contact-form"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSee('name="service_id"', false)
        ->assertSee('Technical Consulting', false)
        ->assertSee('10 Industrial Street, 6th of October, Giza, Egypt', false)
        ->assertSee('Sunday to Thursday, 9:00 AM – 5:00 PM', false)
        ->assertSee('Reply within two hours', false)
        ->assertSee('Choose what suits you', false)
        ->assertSee('Fastest way to reply and send photos of the pump or the fault.', false)
        ->assertSee('output=embed', false)
        ->assertDontSee('6th of October, Giza, Egypt. Sunday to Thursday, 9:00 to 17:00.', false);
});
