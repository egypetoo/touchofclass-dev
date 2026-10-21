<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use App\Services\SiteLayoutDataService;
use Database\Seeders\OrganizationSeeder;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    $this->withoutMiddleware([
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
        LaravelLocalizationRedirectFilter::class,
    ]);
});

it('renders navbar and footer links from the dashboard menus', function () {
    Menu::query()->create([
        'title' => ['en' => 'Dashboard About', 'ar' => 'عن الشركة من الداشبورد'],
        'route_name' => 'about-us',
        'order' => 1,
        'is_active' => true,
        'is_navbar' => true,
        'is_footer' => true,
    ]);

    Menu::query()->create([
        'title' => ['en' => 'Hidden page', 'ar' => 'صفحة مخفية'],
        'route_name' => 'partners.index',
        'order' => 2,
        'is_active' => true,
        'is_navbar' => false,
        'is_footer' => false,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Dashboard About', false)
        ->assertSee(route('about-us'), false)
        ->assertDontSee('Hidden page', false);
});

it('builds view-ready navbar product categories with child products', function () {
    $parent = Category::query()->create([
        'name' => ['en' => 'Pumps', 'ar' => 'طلمبات'],
        'slug' => ['en' => 'pumps', 'ar' => 'pumps'],
        'order' => 1,
        'is_active' => true,
    ]);

    $child = Category::query()->create([
        'parent_id' => $parent->id,
        'name' => ['en' => 'Submersible', 'ar' => 'غاطسة'],
        'slug' => ['en' => 'submersible', 'ar' => 'submersible'],
        'order' => 1,
        'is_active' => true,
    ]);

    $product = Product::query()->create([
        'category_id' => $child->id,
        'name' => ['en' => 'Deep Well Pump', 'ar' => 'طلمبة غاطسة'],
        'slug' => ['en' => 'deep-well-pump', 'ar' => 'deep-well-pump'],
        'code' => 'DWP-1',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $row = app(SiteLayoutDataService::class)->navbarProductCategories()->first();

    expect($row)->toMatchArray([
        'name' => 'Pumps',
        'slug' => 'pumps',
        'url' => route('products.index', ['category' => 'pumps']),
        'count_label' => trans_choice('site.home_products_count', 1, ['count' => 1]),
    ])->and($row['products'])->toHaveCount(1)
        ->and($row['products'][0])->toMatchArray([
            'name' => 'Deep Well Pump',
            'url' => route('products.show', $product),
        ]);
});
