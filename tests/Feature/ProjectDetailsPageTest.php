<?php

use App\Models\Project;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('renders an industry details page for an active project', function () {
    $this->seed(OrganizationSeeder::class);

    $project = Project::query()->create([
        'title' => ['en' => 'Irrigation Network', 'ar' => 'شبكة ري'],
        'slug' => ['en' => 'irrigation-network', 'ar' => 'irrigation-network'],
        'short_text' => ['en' => 'A pumping network', 'ar' => 'شبكة ضخ'],
        'text' => ['en' => '<p>Installed pumps and motors.</p>', 'ar' => '<p>تم تركيب الطلمبات والمواتير.</p>'],
        'features' => ['en' => ['Submersible pumps'], 'ar' => ['طلمبات غاطسة']],
        'is_active' => true,
        'order' => 1,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/projects/'.$project->getTranslation('slug', 'en'));

    $response->assertOk()
        ->assertSee($project->title, false)
        ->assertSee(__('site.recommended_solutions'), false)
        ->assertSee($project->featureList()[0], false)
        ->assertSee('data-quote-form', false);
});
