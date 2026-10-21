<?php

use App\Models\Category;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $this->withoutMiddleware([
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ]);
});

it('redirects category details to the products catalog filtered by that category', function () {
    $category = Category::query()
        ->where('slug->en', 'shipping-boxes')
        ->firstOrFail();

    $this->get('/categories/'.$category->getTranslation('slug', 'en'))
        ->assertMovedPermanently()
        ->assertRedirect(route('products.index', ['category_id' => $category->id]));
});

it('marks the category tab active on the filtered products catalog', function () {
    $category = Category::query()
        ->where('slug->en', 'industrial')
        ->firstOrFail();

    $html = $this->get('/products?category_id='.$category->id)
        ->assertOk()
        ->assertSee('Bulk Pallet Container', false)
        ->assertDontSee('Die-Cut Mailer Box', false)
        ->getContent();

    expect($html)->toMatch('/product-tab-btn active">\s*Industrial \(1\)/');
});
