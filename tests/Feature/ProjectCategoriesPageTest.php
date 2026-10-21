<?php

use App\Models\Project;
use App\Models\ProjectCategory;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('shows every industry card on the industries page without category tabs', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    expect(ProjectCategory::query()->where('is_active', true)->count())->toBe(0)
        ->and(Project::query()->where('is_active', true)->count())->toBe(5);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/projects');

    $response->assertOk()
        ->assertSee('industry-bento-card', false)
        ->assertDontSee('project-tabs', false)
        ->assertSeeInOrder(['Food &amp; Beverage', 'Agriculture', 'Industrial', 'E-Commerce', 'Retail &amp; Gifting'], false)
        ->assertSee('Ventilated packaging for fresh produce.', false);
});
