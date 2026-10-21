<?php

use App\Models\SeoAssistant;
use App\Services\Seo\BuildSitemapService;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

it('normalizes a pasted google search console meta tag to the content token', function () {
    $token = SeoAssistant::normalizeSearchConsoleVerification(
        '<meta name="google-site-verification" content="abc123TOKEN" />'
    );

    expect($token)->toBe('abc123TOKEN');
});

it('stores search console verification and exposes it in the site head', function () {
    SeoAssistant::query()->firstOrCreate(['id' => 1])->update([
        'google_search_console_verification' => '<meta name="google-site-verification" content="site-token-xyz" />',
    ]);

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
        ->toContain('name="google-site-verification"')
        ->toContain('content="site-token-xyz"')
        ->not->toContain('<meta name="google-site-verification" content="<meta');
});

it('omits sitemap sections and static pages that are turned off', function () {
    SeoAssistant::query()->firstOrCreate(['id' => 1])->update([
        'sitemap_sections' => [
            'static' => true,
            'services' => true,
            'blogs' => false,
            'products' => true,
            'projects' => false,
            'pages' => [
                'home' => true,
                'about' => true,
                'contact' => false,
                'services' => true,
                'news' => true,
                'products' => true,
                'projects' => false,
                'partners' => true,
            ],
        ],
    ]);

    app(BuildSitemapService::class)->generateSitemap();

    $index = File::get(public_path('sitemap.xml'));
    $static = File::get(public_path('sitemap_static_en.xml'));

    expect($index)->toContain('sitemap_static_')
        ->toContain('sitemap_services_')
        ->toContain('sitemap_products_')
        ->not->toContain('sitemap_blogs_')
        ->not->toContain('sitemap_projects_')
        ->and($static)->toContain('/news')
        ->not->toContain('/contact-us')
        ->not->toContain('/projects');
});

it('writes a public sitemap xml that is reachable after generation', function () {
    app(BuildSitemapService::class)->generateSitemap();

    $path = public_path('sitemap.xml');

    expect(File::exists($path))->toBeTrue()
        ->and(File::get($path))->toContain('sitemapindex')
        ->and(url('/sitemap.xml'))->toEndWith('/sitemap.xml');
});
