<?php

use App\Enums\BranchTypeEnum;
use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Models\Blog;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Models\Service;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('serves King Motors hero assets and the design scripts on the home page', function () {
    $this->seed(OrganizationSeeder::class);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/');

    $response->assertOk()
        ->assertSee('hero/hero-1.webp', false)
        ->assertSee('fetchpriority="high"', false)
        ->assertSee('rel="preload" as="image"', false)
        ->assertDontSee('Noto+Naskh', false)
        ->assertSee('hero-slider', false)
        ->assertSee('btn-hero-gold', false)
        ->assertSee('lenis.min.js', false)
        ->assertSee('gsap.min.js', false)
        ->assertDontSee('aos.js', false);
});

it('keeps every hero slide clickable in the server-rendered markup', function () {
    $this->seed(OrganizationSeeder::class);

    $html = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->getContent();

    expect(substr_count($html, 'hero-slide'))->toBeGreaterThan(2)
        ->and($html)->not->toMatch('/class="swiper-slide"[^>]*\binert\b/');
});

it('keeps the hero accessibility tree well-formed', function () {
    $this->seed(OrganizationSeeder::class);

    $html = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->getContent();

    expect($html)
        ->not->toContain('class="swiper heroSwiper" aria-hidden="true"')
        ->not->toContain('<p><p>')
        ->toContain('<h1');
});

it('renders the King Motors hero title', function () {
    $this->seed(OrganizationSeeder::class);

    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->assertSee(__('site.home_hero_1_lines')[0], false)
        ->assertSee(__('site.home_hero_1_lines')[1], false);
});

it('ships King Motors hero files under public assets', function () {
    foreach ([...Path::heroDesignImages(), ...Path::heroDesignMobileImages()] as $relative) {
        expect(is_file(public_path('assets/images/'.$relative)))->toBeTrue();
    }
});

it('renders King Motors home section shells', function () {
    $this->seed(OrganizationSeeder::class);

    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->assertSee('services-km', false)
        ->assertSee('km-products', false)
        ->assertSee('about-km', false)
        ->assertSee('km-selector', false)
        ->assertSee('km-why', false)
        ->assertSee('km-articles', false)
        ->assertSee('km-contact', false)
        ->assertSee('id="contact-form"', false);
});

it('renders the hero from a dashboard section', function () {
    $this->seed(OrganizationSeeder::class);

    Section::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::HERO->value,
        'title' => ['en' => "Dashboard headline\nsecond line", 'ar' => "عنوان من الداشبورد\nالسطر الثاني"],
        'text' => ['en' => 'Dashboard body', 'ar' => 'نص من الداشبورد'],
        'button_text' => ['en' => 'Dashboard button', 'ar' => 'زر الداشبورد'],
        'order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('Dashboard headline')
        ->toContain('Dashboard body')
        ->not->toContain('لضخ المياه')
        ->toMatch('/<img[^>]*class="hero-mobile-img"[^>]*src="[^"]*hero\/hero-1\.webp"/');
});

it('renders the home projects intro from the featured section', function () {
    $this->seed(OrganizationSeeder::class);

    Section::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::FEATURED_PRODUCTS->value,
        'sub_title' => ['en' => 'Our work', 'ar' => 'أعمالنا'],
        'title' => ['en' => 'Selected', 'ar' => 'مشاريع'],
        'second_title' => ['en' => 'projects', 'ar' => 'مختارة'],
        'text' => ['en' => 'Selected project intro', 'ar' => 'مقدمة المشاريع'],
        'button_text' => ['en' => 'View all projects', 'ar' => 'عرض كل المشاريع'],
        'button_link_url' => '/projects',
        'order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('Our work')
        ->toContain('Selected')
        ->toContain('projects')
        ->toContain('Selected project intro')
        ->toContain('studio/urban.jpg');
});

it('renders home project tabs from categories', function () {
    $this->seed(OrganizationSeeder::class);

    $category = ProjectCategory::query()->create([
        'name' => ['en' => 'Residential', 'ar' => 'سكني'],
        'short_text' => ['en' => 'Villas and apartments', 'ar' => 'فلل وشقق'],
        'slug' => ['en' => 'residential', 'ar' => 'residential'],
        'icon' => 'fa-solid fa-gears',
        'order' => 1,
        'is_active' => true,
    ]);

    Project::query()->create([
        'project_category_id' => $category->id,
        'title' => ['en' => 'Sheikh Zayed Villa', 'ar' => 'فيلا الشيخ زايد'],
        'slug' => ['en' => 'sheikh-zayed-villa', 'ar' => 'sheikh-zayed-villa'],
        'order' => 1,
        'is_active' => true,
        'show_in_home' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('data-cat="residential"')
        ->toContain('id="km-pane-residential"')
        ->toContain('Residential')
        ->toContain('Sheikh Zayed Villa');
});

it('renders the why us section from dashboard sections', function () {
    $this->seed(OrganizationSeeder::class);

    $organizationId = Organization::query()->value('id');

    Section::query()->create([
        'organization_id' => $organizationId,
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::WHY_CHOOSE_US->value,
        'sub_title' => ['en' => 'Why us', 'ar' => 'لماذا نحن'],
        'title' => ['en' => "Why choose\nTouch of Class?", 'ar' => "لماذا تختار\nTouch of Class؟"],
        'text' => ['en' => 'Dashboard why text', 'ar' => 'نص لماذا نحن'],
        'order' => 1,
        'is_active' => true,
    ]);

    Section::query()->create([
        'organization_id' => $organizationId,
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::BENEFITS->value,
        'title' => ['en' => 'Guaranteed quality', 'ar' => 'جودة مضمونة'],
        'text' => ['en' => 'Dashboard benefit', 'ar' => 'ميزة من الداشبورد'],
        'order' => 1,
        'is_active' => true,
    ]);

    Section::query()->create([
        'organization_id' => $organizationId,
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::STATISTICS->value,
        'title' => ['en' => '15+', 'ar' => '15+'],
        'text' => ['en' => 'Years in the market', 'ar' => 'سنة خبرة في السوق'],
        'order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('Why us')
        ->toContain('Why choose')
        ->toContain('Touch of Class?')
        ->toContain('Dashboard why text')
        ->toContain('Guaranteed quality')
        ->toContain('Dashboard benefit')
        ->toContain('data-count="15"')
        ->toContain('Years in the market');
});

it('renders the home blogs heading from the blogs section', function () {
    $this->seed(OrganizationSeeder::class);

    Section::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::BLOGS->value,
        'sub_title' => ['en' => 'Our articles', 'ar' => 'مقالاتنا'],
        'title' => ['en' => 'Latest', 'ar' => 'أحدث'],
        'second_title' => ['en' => 'articles', 'ar' => 'المقالات'],
        'text' => ['en' => 'Dashboard articles text', 'ar' => 'نص المقالات'],
        'order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('Our articles')
        ->toContain('Latest')
        ->toContain('articles')
        ->toContain('Dashboard articles text');
});

it('renders home articles from blogs', function () {
    $this->seed(OrganizationSeeder::class);

    $organizationId = Organization::query()->value('id');

    Blog::query()->create([
        'organization_id' => $organizationId,
        'title' => ['en' => 'Newer home article', 'ar' => 'مقال أحدث'],
        'excerpt' => ['en' => 'Newer excerpt', 'ar' => 'مقتطف أحدث'],
        'content' => ['en' => '<p>Newer excerpt</p>', 'ar' => '<p>مقتطف أحدث</p>'],
        'is_active' => true,
        'published_at' => '2025-09-15',
    ]);

    Blog::query()->create([
        'organization_id' => $organizationId,
        'title' => ['en' => 'Older home article', 'ar' => 'مقال أقدم'],
        'excerpt' => ['en' => 'Older excerpt', 'ar' => 'مقتطف أقدم'],
        'content' => ['en' => '<p>Older excerpt</p>', 'ar' => '<p>مقتطف أقدم</p>'],
        'is_active' => true,
        'published_at' => '2025-08-10',
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    $html = $response->getContent();

    expect($html)
        ->toMatch('/is-featured[\s\S]{0,1200}Newer home article/')
        ->toContain('Older home article')
        ->toContain('studio/photo-1.jpg')
        ->toContain('studio/photo-2.jpg')
        ->toContain('/news/');
});

it('renders the home contact section from the dashboard', function () {
    $this->seed(OrganizationSeeder::class);

    $organizationId = Organization::query()->value('id');

    Section::query()->create([
        'organization_id' => $organizationId,
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::CONTACT_US_CONTENT->value,
        'sub_title' => ['en' => 'Call the studio', 'ar' => 'اتصل بالاستوديو'],
        'title' => ['en' => "Talk\nwith us", 'ar' => "تواصل\nمعنا"],
        'text' => ['en' => 'Dashboard contact text', 'ar' => 'نص التواصل'],
        'order' => 1,
        'is_active' => true,
    ]);

    Branch::query()->create([
        'organization_id' => $organizationId,
        'type' => BranchTypeEnum::Main,
        'name' => ['en' => 'Studio', 'ar' => 'الاستوديو'],
        'address' => ['en' => 'Studio contact address', 'ar' => 'عنوان الاستوديو'],
        'phone_1_country_code' => '20',
        'phone_1' => '1120846811',
        'email' => 'studio@touchofclass.com',
        'working_hours' => ['en' => 'Saturday to Thursday, 9:00 AM – 6:00 PM', 'ar' => 'من السبت إلى الخميس، 9:00 صباحًا – 6:00 مساءً'],
        'map_url' => 'https://maps.google.com/maps?q=Studio',
        'map_embed' => 'https://maps.google.com/maps?q=Studio&output=embed',
        'is_active' => true,
        'order' => 1,
    ]);

    Service::query()->create([
        'organization_id' => $organizationId,
        'title' => ['en' => 'Interior request', 'ar' => 'طلب تصميم'],
        'is_active' => true,
        'order' => 1,
    ]);

    Cache::flush();

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('Call the studio')
        ->toContain('Talk')
        ->toContain('with us')
        ->toContain('Dashboard contact text')
        ->toContain('Studio contact address')
        ->toContain('tel:+201120846811')
        ->toContain('studio@touchofclass.com')
        ->toContain('Saturday to Thursday')
        ->toContain('9:00 AM – 6:00 PM')
        ->toContain('Interior request')
        ->toContain(route('contact.store'))
        ->toContain('q=Studio');
});

it('renders the services heading from the title and second title', function () {
    $this->seed(OrganizationSeeder::class);

    Section::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::SERVICES->value,
        'sub_title' => ['en' => 'Our services', 'ar' => 'خدماتنا'],
        'title' => ['en' => 'Our', 'ar' => 'خدماتنا'],
        'second_title' => ['en' => 'main services', 'ar' => 'الرئيسية'],
        'order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('services-km-eyebrow-text">Our services')
        ->toContain('services-km-title-dark">Our')
        ->toContain('services-km-title-gold">main services');
});

it('renders the home about section from the dashboard', function () {
    $this->seed(OrganizationSeeder::class);

    Section::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'page' => PageEnum::HOME->value,
        'type' => SectionTypeEnum::ABOUTUS->value,
        'sub_title' => ['en' => 'About the company', 'ar' => 'عن الشركة'],
        'title' => ['en' => "Experience that reaches\nevery space you live in", 'ar' => "خبرة تمتد\nلكل مساحة تعيش فيها"],
        'text' => ['en' => 'Dashboard about body', 'ar' => 'نص عن الشركة من الداشبورد'],
        'button_text' => ['en' => 'Learn more about us', 'ar' => 'اعرف المزيد عنا'],
        'button_link_url' => '/about-us',
        'order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk();

    expect($response->getContent())
        ->toContain('About the company')
        ->toContain('Experience that reaches')
        ->toContain('every space you live in')
        ->toContain('Dashboard about body')
        ->toContain('Learn more about us')
        ->toContain('href="/about-us"')
        ->toContain('studio/about-main.jpg')
        ->toContain('studio/interior-design.jpg');
});
