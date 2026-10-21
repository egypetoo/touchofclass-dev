<?php

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

function homeFilterCategory(string $slug, ?Category $parent = null, int $order = 0): Category
{
    return Category::query()->create([
        'parent_id' => $parent?->id,
        'name' => ['en' => ucfirst($slug), 'ar' => $slug],
        'slug' => ['en' => $slug, 'ar' => $slug],
        'order' => $order,
        'is_active' => true,
    ]);
}

function homeFilterProduct(Category $category, string $code, int $sortOrder): Product
{
    return Product::query()->create([
        'category_id' => $category->id,
        'name' => ['en' => "Product {$code}", 'ar' => "منتج {$code}"],
        'slug' => ['en' => strtolower($code), 'ar' => strtolower($code)],
        'code' => $code,
        'sort_order' => $sortOrder,
        'is_active' => true,
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

it('shows subcategory products inside their root category on the home page', function () {
    $bar = homeFilterCategory('bar', order: 1);
    $hall = homeFilterCategory('hall', order: 2);
    $trays = homeFilterCategory('trays', parent: $hall);

    foreach (range(1, 8) as $position) {
        homeFilterProduct($bar, "BAR-{$position}", $position);
    }

    homeFilterProduct($trays, 'TRAY-1', 99);

    $html = homeFilterPageHtml();

    $hallStart = strpos($html, 'id="km-pane-hall"');
    $hallEnd = strpos($html, 'tab-pane', $hallStart + 10);
    $hallPane = substr($html, $hallStart, ($hallEnd === false ? strlen($html) : $hallEnd) - $hallStart);

    expect($html)
        ->toContain('data-cat="hall"')
        ->toContain('Product TRAY-1')
        ->and($hallPane)->toContain('tray-1')->not->toContain('Product BAR-1');

    foreach (range(1, 8) as $position) {
        expect($html)->toContain("Product BAR-{$position}");
    }
});
