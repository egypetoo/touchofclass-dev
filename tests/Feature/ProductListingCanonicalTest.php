<?php

use App\Models\Category;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

function productListingHtml(string $uri): string
{
    return test()
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get($uri)
        ->assertOk()
        ->getContent();
}

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    config([
        'app.locale' => 'ar',
        'laravellocalization.hideDefaultLocaleInURL' => false,
    ]);
});

it('keeps the category filter in the canonical and hreflang urls of the products listing', function () {
    $category = Category::query()->create([
        'name' => ['en' => 'Shipping Boxes', 'ar' => 'كراتين الشحن'],
        'slug' => ['en' => 'shipping-boxes', 'ar' => 'shipping-boxes-ar'],
        'is_active' => true,
    ]);

    $html = productListingHtml("/products?sort=name_asc&page=1&category_id={$category->id}");

    expect($html)
        ->toContain('<link rel="canonical" href="'.url("/products?category_id={$category->id}").'">')
        ->toContain('hreflang="en" href="'.url("/en/products?category_id={$category->id}").'"')
        ->toMatch('/<title>[^<]*(Shipping Boxes|كراتين الشحن)[^<]*<\/title>/u');
});

it('uses the bare products url as canonical when no filter is applied', function () {
    $html = productListingHtml('/products?sort=name_asc');

    expect($html)->toContain('<link rel="canonical" href="'.url('/products').'">');
});
