<?php

use App\Enums\ClientSectorEnum;
use App\Enums\ProductStatusEnum;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Product;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\MenuSeeder;
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

it('renders the real catalog on the home products section', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Mivida', false)
        ->assertSee('data-cat="residential"', false)
        ->assertSee('data-cat="architecture"', false)
        ->assertDontSee('موتور كهربائي IE3', false)
        ->assertDontSee('(280 منتج)', false);
});

it('renders the King Motors about page', function () {
    $this->get('/about-us')
        ->assertOk()
        ->assertSee('km-page-title', false)
        ->assertSee(__('site.about_page_title'), false);
});

it('renders the main categories page from the products menu', function () {
    $this->seed(MenuSeeder::class);

    $this->get('/categories')
        ->assertOk()
        ->assertSee('km-cat-card', false)
        ->assertSee('Residential', false)
        ->assertSee('Commercial', false)
        ->assertSee('Architecture', false)
        ->assertSee(route('products.index', ['category' => 'residential']), false)
        ->assertSee(route('categories.index'), false);
});

it('renders the King Motors products catalog', function () {
    $this->seed(MenuSeeder::class);

    $this->get('/products')
        ->assertOk()
        ->assertSee('km-shop', false)
        ->assertSee('Residential', false)
        ->assertSee('<span>Residential</span><small>6</small>', false)
        ->assertDontSee('منتج واحد', false)
        ->assertSee('Sheikh Zayed Villa', false)
        ->assertSee('residential', false)
        ->assertSee(__('site.our_products'), false)
        ->assertSee(__('site.home_view_details'), false)
        ->assertDontSee('موتور كهربائي IE3', false)
        ->assertDontSee('منتجاتنا', false);
});

it('filters the catalog by category, brand, and search', function () {
    $this->get('/products?category=commercial')
        ->assertOk()
        ->assertSee('Dallas Villa', false)
        ->assertDontSee('Sheikh Zayed Villa', false)
        ->assertDontSee('Lake House', false);

    $this->get('/products?category=renovation')
        ->assertOk()
        ->assertSee('TOC-REN-01', false)
        ->assertDontSee('TOC-RES-01', false);

    $this->get('/products?category=architecture')
        ->assertOk()
        ->assertSee('TOC-ARC-01', false)
        ->assertDontSee('TOC-REN-01', false);

    $this->get('/products?brand[]=Penthouse')
        ->assertOk()
        ->assertSee('TOC-RES-03', false)
        ->assertDontSee('TOC-COM-02', false);

    $this->get('/products?q=Brooklyn')
        ->assertOk()
        ->assertSee('TOC-REN-04', false)
        ->assertDontSee('TOC-RES-01', false);
});

it('renders the product details page from the new design', function () {
    $product = Product::query()->where('code', 'TOC-RES-03')->firstOrFail();

    $this->get('/products/'.$product->getTranslation('slug', 'en'))
        ->assertOk()
        ->assertSee('km-pd', false)
        ->assertSee($product->name, false)
        ->assertSee('TOC-RES-03', false)
        ->assertSee('penthouse.jpg', false)
        ->assertDontSee('42 m³/h', false)
        ->assertDontSee('GRUNDFOS', false)
        ->assertSee(__('site.home_add_to_cart'), false)
        ->assertSee(__('site.product_specs'), false)
        ->assertDontSee('أضف للسلة', false);
});

it('renders the services index with the design cards', function () {
    $this->get('/services')
        ->assertOk()
        ->assertSee('service-card', false)
        ->assertSee('Interior Design', false)
        ->assertSee('Architectural Design', false)
        ->assertSee('Master Planning', false)
        ->assertDontSee('التصميم الداخلي', false);
});

it('renders the clients page without retired packaging clients', function () {
    $this->get('/partners')
        ->assertOk()
        ->assertDontSee('Danone', false)
        ->assertSee('Interior', false)
        ->assertDontSee(__('site.no_items_yet'), false);
});

it('lists clients without a sector under other clients and hides inactive clients', function () {
    $organizationId = Partner::query()->value('organization_id');

    Partner::query()->create([
        'organization_id' => $organizationId,
        'name' => ['en' => 'Unsorted Client', 'ar' => 'Unsorted Client'],
        'is_active' => true,
        'order' => 500,
    ]);
    Partner::query()->create([
        'organization_id' => $organizationId,
        'name' => ['en' => 'Hidden Client', 'ar' => 'Hidden Client'],
        'sector' => ClientSectorEnum::Ceramics->value,
        'is_active' => false,
        'order' => 501,
    ]);

    $this->get('/partners')
        ->assertOk()
        ->assertSee(__('site.other_clients'), false)
        ->assertSee('Unsorted Client', false)
        ->assertDontSee('Hidden Client', false);
});

it('renders catalog pagination on the products page', function () {
    $category = Category::query()->where('is_active', true)->firstOrFail();

    for ($i = 1; $i <= 15; $i++) {
        Product::query()->create([
            'category_id' => $category->id,
            'name' => ['en' => "Extra Product {$i}", 'ar' => "منتج إضافي {$i}"],
            'slug' => ['en' => "extra-product-{$i}", 'ar' => "extra-product-{$i}"],
            'code' => 'EXTRA-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'is_active' => true,
            'status' => ProductStatusEnum::FEATURED->value,
            'sort_order' => 100 + $i,
        ]);
    }

    $this->get('/products')
        ->assertOk()
        ->assertSee('km-shop-grid', false)
        ->assertSee('page=2', false)
        ->assertSee('Sheikh Zayed Villa', false)
        ->assertDontSee('TOC-ARC-01', false);
});

it('renders the King Motors news, cart and pump selector pages', function () {
    $this->get('/news')
        ->assertOk()
        ->assertSee(__('site.news_and_articles'), false)
        ->assertSee('A guide to choosing the right design style for your home', false)
        ->assertDontSee('أخبار ومقالات', false);
    $this->get('/pump-selector')->assertOk()->assertSee('km-selector', false);
    $this->get('/cart')->assertOk()->assertSee('km-cart', false);

    $blog = Blog::query()->where('slug->en', 'design-style-guide')->firstOrFail();

    $this->get('/news/'.$blog->getTranslation('slug', 'en'))
        ->assertOk()
        ->assertSee($blog->title, false);
});

it('renders the King Motors page header on inner pages', function () {
    $this->get('/about-us')
        ->assertOk()
        ->assertSee('km-page-header', false)
        ->assertSee(__('site.about_page_title'), false);
});
