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

    expect(ProjectCategory::query()->where('is_active', true)->count())->toBe(4)
        ->and(Project::query()->where('is_active', true)->count())->toBe(10);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/projects');

    $response->assertOk()
        ->assertSee('km-page-header', false)
        ->assertSee(__('site.projects'), false)
        ->assertSee('km-projects-grid', false)
        ->assertDontSee('km-products-swiper', false)
        ->assertSee('km-album', false)
        ->assertSee('data-cat="residential"', false)
        ->assertDontSee('industry-bento-card', false)
        ->assertDontSee('/projects/mivida', false)
        ->assertSee('Mivida', false)
        ->assertSee('Hayaty Residence', false);

    $commercial = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/projects?category=commercial')
        ->assertOk();

    expect($commercial->getContent())
        ->toContain('class="km-tab w-100 h-100 active" id="km-tab-commercial"')
        ->toContain('class="tab-pane fade show active" id="km-pane-commercial"')
        ->toContain('Restaurant');
});
