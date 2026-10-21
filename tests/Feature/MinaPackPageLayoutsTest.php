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
        ->assertSee('KM-P-VANSAN', false)
        ->assertSee('data-cat="deep-well-motors"', false)
        ->assertSee('data-cat="electrical-panels"', false)
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
        ->assertSee('Deep Well Pumps', false)
        ->assertSee('Deep Well Motors', false)
        ->assertSee('Electrical Panels', false)
        ->assertSee(route('products.index', ['category' => 'deep-well-pumps']), false)
        ->assertSee(route('categories.index'), false);
});

it('renders the King Motors products catalog', function () {
    $this->seed(MenuSeeder::class);

    $this->get('/products')
        ->assertOk()
        ->assertSee('km-shop', false)
        ->assertSee('Deep Well Pumps', false)
        ->assertSee(trans_choice('site.home_products_count', 5, ['count' => 5]), false)
        ->assertDontSee('منتج واحد', false)
        ->assertSee('VAN SAN', false)
        ->assertSee('deep-well-pumps', false)
        ->assertSee(__('site.our_products'), false)
        ->assertSee(__('site.home_view_details'), false)
        ->assertDontSee('موتور كهربائي IE3', false)
        ->assertDontSee('منتجاتنا', false);
});

it('filters the catalog by category, brand, and search', function () {
    $this->get('/products?category=deep-well-motors')
        ->assertOk()
        ->assertSee('POLDAP', false)
        ->assertDontSee('Bore Power', false)
        ->assertDontSee('Pure Life', false);

    $this->get('/products?category=submersible-cables')
        ->assertOk()
        ->assertSee('KM-C-AXELON', false)
        ->assertDontSee('KM-P-VANSAN', false);

    $this->get('/products?category=electrical-panels')
        ->assertOk()
        ->assertSee('KM-INV-DELIXI', false)
        ->assertDontSee('KM-C-AXELON', false);

    $this->get('/products?brand[]=SP')
        ->assertOk()
        ->assertSee('KM-P-SP', false)
        ->assertSee('KM-M-SP', false)
        ->assertDontSee('KM-P-TESLA', false);

    $this->get('/products?q=TESLA')
        ->assertOk()
        ->assertSee('KM-P-TESLA', false)
        ->assertDontSee('KM-P-VANSAN', false);
});

it('renders the product details page from the new design', function () {
    $product = Product::query()->where('code', 'KM-P-SP')->firstOrFail();

    $this->get('/products/'.$product->getTranslation('slug', 'en'))
        ->assertOk()
        ->assertSee('km-pd', false)
        ->assertSee($product->name, false)
        ->assertSee('KM-P-SP', false)
        ->assertSee('km-p-sp.jpg', false)
        ->assertSee('42 m³/h', false)
        ->assertSee('SP pumps data', false)
        ->assertDontSee('GRUNDFOS', false)
        ->assertSee(__('site.home_add_to_cart'), false)
        ->assertSee(__('site.product_specs'), false)
        ->assertDontSee('أضف للسلة', false);
});

it('renders the services index with the design cards', function () {
    $this->get('/services')
        ->assertOk()
        ->assertSee('service-card', false)
        ->assertSee('Technical Consulting', false)
        ->assertSee('Spare Parts', false)
        ->assertSee('Supply and Installation', false)
        ->assertDontSee('استشارات', false);
});

it('renders the clients page without retired packaging clients', function () {
    $this->get('/partners')
        ->assertOk()
        ->assertDontSee('Danone', false)
        ->assertSee('VAN SAN', false)
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
        ->assertSee('VAN SAN', false)
        ->assertDontSee('KM-C-AXELON', false);
});

it('renders the King Motors news, cart and pump selector pages', function () {
    $this->get('/news')
        ->assertOk()
        ->assertSee(__('site.news_and_articles'), false)
        ->assertSee('A complete guide to choosing the right pump', false)
        ->assertDontSee('أخبار ومقالات', false);
    $this->get('/pump-selector')->assertOk()->assertSee('km-selector', false);
    $this->get('/cart')->assertOk()->assertSee('km-cart', false);

    $blog = Blog::query()->where('slug->en', 'pump-selection-guide')->firstOrFail();

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
