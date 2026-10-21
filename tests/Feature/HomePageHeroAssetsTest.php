<?php

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Models\Organization;
use App\Models\Section;
use Database\Seeders\OrganizationSeeder;
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

    $this
        ->withoutMiddleware([
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])
        ->get('/')
        ->assertOk()
        ->assertSee('Dashboard headline', false)
        ->assertSee('Dashboard body', false)
        ->assertDontSee('لضخ المياه', false);
});
