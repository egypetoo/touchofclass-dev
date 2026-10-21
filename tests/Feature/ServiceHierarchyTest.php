<?php

use App\Models\Service;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

function childSolution(Service $parent, string $title, int $order): Service
{
    return Service::query()->create([
        'organization_id' => $parent->organization_id,
        'parent_id' => $parent->id,
        'title' => ['en' => $title, 'ar' => $title],
        'slug' => ['en' => Str::slug($title), 'ar' => Str::slug($title)],
        'is_active' => true,
        'order' => $order,
    ]);
}

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $this->parentSolution = Service::query()
        ->whereNull('parent_id')
        ->where('slug->en', 'technical-consulting')
        ->firstOrFail();

    childSolution($this->parentSolution, 'Single Wall Cartons', 1);
    childSolution($this->parentSolution, 'Double Wall Cartons', 2);

    $this->withoutMiddleware([
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ]);
});

it('lists the design services and hides child solutions', function () {
    $this->get('/services')
        ->assertOk()
        ->assertSee('Technical Consulting', false)
        ->assertDontSee('Single Wall Cartons', false);
});

it('shows children when a parent solution is opened', function () {
    $this->get('/services/'.$this->parentSolution->getTranslation('slug', 'en'))
        ->assertOk()
        ->assertSee('solution-grid-card', false)
        ->assertSee('Single Wall Cartons', false)
        ->assertSee('Double Wall Cartons', false)
        ->assertDontSee('details-content', false);
});

it('opens service details directly when the service has no children', function () {
    $leaf = Service::query()
        ->where('slug->en', 'warranty-maintenance')
        ->firstOrFail();

    expect($leaf->children()->count())->toBe(0);

    $this->get('/services/'.$leaf->getTranslation('slug', 'en'))
        ->assertOk()
        ->assertSee('km-sd-sidebar', false)
        ->assertSee($leaf->title, false)
        ->assertSee('km-sd-help-form', false);
});
