<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\Mail\MailConfigService;
use App\View\Composers\SeoComposer;
use App\View\Composers\SiteLayoutComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;

class AppServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());

        // Prevent lazy loading in production
        Model::preventLazyLoading(! app()->isProduction());
        Paginator::useBootstrapFive();

        try {
            app(MailConfigService::class)->apply();
        } catch (\Throwable) {
            // DB may not be ready during early install/migrate.
        }

        try {
            $this->applySiteDefaultLocale();
        } catch (\Throwable) {
            // DB may not be ready during early install/migrate.
        }

        // layouts.site: header, footer, modals. site.*: @section content (e.g. breadcrumbImage).
        View::composer(
            ['layouts.site', 'site.*'],
            SiteLayoutComposer::class
        );

        View::composer(
            ['layouts.site', 'site.*'],
            SeoComposer::class
        );
    }

    protected function applySiteDefaultLocale(): void
    {
        $locale = Cache::remember('site_default_locale', now()->addMinutes(5), function (): ?string {
            return SiteSetting::query()
                ->whereNotNull('organization_id')
                ->orderBy('id')
                ->value('default_locale')
                ?? SiteSetting::query()->orderBy('id')->value('default_locale');
        });

        if (! filled($locale)) {
            return;
        }

        $supported = config('laravellocalization.supportedLocales', []);
        if (! is_array($supported) || ! array_key_exists($locale, $supported)) {
            return;
        }

        config([
            'app.locale' => $locale,
            'laravellocalization.useAcceptLanguageHeader' => false,
        ]);

        app()->setLocale($locale);

        // Keep mcamara's cached defaultLocale in sync (it is read once at construction).
        if (app()->bound('laravellocalization')) {
            $localization = app('laravellocalization');
            $reflection = new \ReflectionProperty($localization, 'defaultLocale');
            $reflection->setValue($localization, $locale);
        }
    }
}
