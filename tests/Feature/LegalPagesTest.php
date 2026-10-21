<?php

use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('renders privacy policy and terms pages without server errors', function (string $path) {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get($path);

    $response->assertOk();
})->with([
    '/privacy-policy',
    '/terms-and-conditions',
]);
