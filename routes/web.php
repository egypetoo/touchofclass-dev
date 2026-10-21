<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\PerformanceMetricController;
use App\Http\Controllers\Site\AboutUsController;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CategoryController;
use App\Http\Controllers\Site\ContactUsController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\NewsletterSubscriberController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\PartnerController;
use App\Http\Controllers\Site\ProductController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\PumpSelectorController;
use App\Http\Controllers\Site\ServiceController;
use App\Http\Controllers\SwitchAdminLocaleController;
use App\Http\Middleware\SetAdminLocale;
use App\Services\Seo\BuildSitemapService;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('robots.txt', function () {
    $path = public_path('robots.txt');
    if (! file_exists($path)) {
        app(BuildSitemapService::class)->generateSitemap();
    }

    return response(file_get_contents($path), 200, ['Content-Type' => 'text/plain']);
});

Route::get('manifest.json', ManifestController::class)->name('manifest');
Route::get('admin/locale/{locale}', SwitchAdminLocaleController::class)
    ->whereIn('locale', SetAdminLocale::SUPPORTED_LOCALES)
    ->name('admin.locale');
Route::post('api/performance-metrics', [PerformanceMetricController::class, 'store'])
    ->name('performance-metrics.store');

Route::prefix(LaravelLocalization::setLocale())
    ->middleware(['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('about-us', AboutUsController::class)->name('about-us');
        Route::get('contact-us', [ContactUsController::class, 'index'])->name('contact-us')->middleware('web');
        Route::post('contact-us', [ContactUsController::class, 'store'])->name('contact.store')->middleware('web');
        Route::get('thank-you', [ContactUsController::class, 'thanks'])->name('contact.thanks');
        Route::post('newsletter/subscribe', [NewsletterSubscriberController::class, 'store'])
            ->name('newsletter.subscribe')
            ->middleware('web');
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::get('news', [NewsController::class, 'index'])->name('news.index');
        Route::get('news/{blog}', [NewsController::class, 'show'])->name('news.show');
        Route::get('pump-selector', PumpSelectorController::class)->name('pump-selector');
        Route::get('cart', [CartController::class, 'index'])->name('cart');
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::post('cart/quote', [CartController::class, 'quote'])->name('cart.quote');
        Route::post('cart/{product}/qty', [CartController::class, 'update'])->name('cart.update');
        Route::post('cart/{product}/remove', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::get('partners', [PartnerController::class, 'index'])->name('partners.index');

        Route::controller(CategoryController::class)
            ->prefix('categories')
            ->name('categories.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{category}', 'show')->name('show');
            });

        Route::controller(ProductController::class)
            ->prefix('products')
            ->name('products.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{product}', 'show')->name('show');
            });

        Route::controller(ProjectController::class)
            ->prefix('projects')
            ->name('projects.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{project}', 'show')->name('show');
            });

        Route::controller(PageController::class)->group(function () {
            Route::get('privacy-policy', 'privacyPolicy')->name('privacy-policy');
            Route::get('terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');
        });
    });
