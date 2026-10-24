<?php

use App\Enums\BranchTypeEnum;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Services\SiteLayoutDataService;
use Database\Seeders\MenuSeeder;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
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

it('renders the design navigation without the old product and pump links', function () {
    $this->seed(MenuSeeder::class);

    $this->get('/')
        ->assertOk()
        ->assertSee('Projects', false)
        ->assertSee('Residential', false)
        ->assertSee('Architecture', false)
        ->assertSee(route('projects.index'), false)
        ->assertSee(route('projects.index', ['category' => 'commercial']), false)
        ->assertDontSee('Pump Selector', false);
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

it('renders the footer from dashboard content', function () {
    ProjectCategory::query()->create([
        'name' => ['en' => 'Footer Residential', 'ar' => 'سكني الفوتر'],
        'is_active' => true,
        'order' => 1,
    ]);

    $organizationId = Organization::query()->value('id');

    $service = Service::query()->create([
        'organization_id' => $organizationId,
        'title' => ['en' => 'Footer Interior', 'ar' => 'تصميم الفوتر'],
        'is_active' => true,
        'order' => 1,
    ]);

    Branch::query()->create([
        'organization_id' => $organizationId,
        'type' => BranchTypeEnum::Main,
        'name' => ['en' => 'Studio', 'ar' => 'الاستوديو'],
        'address' => ['en' => 'Footer studio address', 'ar' => 'عنوان الفوتر'],
        'map_url' => 'https://maps.google.com/maps?q=FooterStudio',
        'phone_1_country_code' => '20',
        'phone_1' => '1120846811',
        'email' => 'footer@touchofclass.com',
        'working_hours' => ['en' => 'Sunday to Thursday, 9:00 AM – 5:00 PM', 'ar' => 'من الأحد إلى الخميس، 9:00 صباحاً – 5:00 مساءً'],
        'is_active' => true,
        'order' => 1,
    ]);

    SiteSetting::query()->updateOrCreate(
        ['organization_id' => $organizationId],
        [
            'footer_body_en' => 'Footer studio description',
            'footer_copyright_en' => '© 2026 Touch of Class. All rights reserved.',
        ],
    );

    Cache::flush();

    $this->get('/')
        ->assertOk()
        ->assertSee('Footer Residential', false)
        ->assertSee('Footer Interior', false)
        ->assertSee(route('services.show', $service), false)
        ->assertSee('Footer studio address', false)
        ->assertSee('https://maps.google.com/maps?q=FooterStudio', false)
        ->assertSee('footer@touchofclass.com', false)
        ->assertSee('tel:+201120846811', false)
        ->assertSee('Sunday to Thursday', false)
        ->assertSee('Footer studio description', false)
        ->assertSee('© 2026 Touch of Class. All rights reserved.', false)
        ->assertSee(route('projects.index'), false)
        ->assertSee(route('services.index'), false)
        ->assertSee(route('contact-us'), false);
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
