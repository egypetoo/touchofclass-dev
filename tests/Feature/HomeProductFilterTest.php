<?php

use App\Models\Project;
use App\Models\ProjectCategory;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

function homeProjectCategory(string $slug, int $order = 0): ProjectCategory
{
    return ProjectCategory::query()->create([
        'name' => ['en' => ucfirst($slug), 'ar' => $slug],
        'slug' => ['en' => $slug, 'ar' => $slug],
        'order' => $order,
        'is_active' => true,
    ]);
}

function homeProject(ProjectCategory $category, string $slug, int $order): Project
{
    return Project::query()->create([
        'project_category_id' => $category->id,
        'title' => ['en' => 'Project '.$slug, 'ar' => 'مشروع '.$slug],
        'slug' => ['en' => $slug, 'ar' => $slug],
        'order' => $order,
        'is_active' => true,
        'show_in_home' => true,
    ]);
}

function homeFilterPageHtml(): string
{
    return test()
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->getContent();
}

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);
});

it('shows each home project inside its project category', function () {
    $residential = homeProjectCategory('residential', 1);
    $commercial = homeProjectCategory('commercial', 2);

    homeProject($residential, 'sheikh-zayed-villa', 1);
    homeProject($commercial, 'dallas-villa', 2);

    $html = homeFilterPageHtml();

    $paneStart = strpos($html, 'id="km-pane-residential"');
    $paneEnd = strpos($html, 'tab-pane', $paneStart + 10);
    $residentialPane = substr($html, $paneStart, ($paneEnd === false ? strlen($html) : $paneEnd) - $paneStart);

    expect($html)
        ->toContain('data-cat="residential"')
        ->toContain('Project sheikh-zayed-villa')
        ->toContain('Project dallas-villa')
        ->and($residentialPane)->toContain('sheikh-zayed-villa')->not->toContain('dallas-villa');
});
