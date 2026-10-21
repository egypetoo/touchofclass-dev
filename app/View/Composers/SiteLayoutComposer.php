<?php

namespace App\View\Composers;

use App\Services\SessionCart;
use App\Services\SiteLayoutDataService;
use App\Support\LocaleUrl;
use Illuminate\View\View;

class SiteLayoutComposer
{
    public function __construct(
        private SiteLayoutDataService $layoutData,
        private SessionCart $cart,
    ) {}

    public function compose(View $view): void
    {
        $siteSetting = $this->layoutData->siteSetting();
        $mainBranch = $this->layoutData->mainBranch();
        $switchLocale = collect(array_keys(config('laravellocalization.supportedLocales', [])))
            ->first(fn (string $locale): bool => $locale !== app()->getLocale());

        $view->with([
            'mainMenu' => $this->layoutData->mainNavigation(),
            'mainServices' => $this->layoutData->services(),
            'siteSetting' => $siteSetting,
            'footerNavigation' => $this->layoutData->footerNavigation(),
            'mainBranch' => $mainBranch,
            'branches' => $this->layoutData->branches(),
            'localeSwitchUrl' => fn (string $locale): string => LocaleUrl::forLocale($locale),
            'switchLocale' => $switchLocale,
            'siteLogoUrl' => $siteSetting?->logoUrl() ?? asset('assets/images/logo/logo-transparent.webp'),
            'siteDisplayName' => $siteSetting?->siteName() ?? 'King Motors',
            'bookLabel' => $siteSetting?->ctaLabel() ?: __('site.book_now'),
            'primaryPhone' => $siteSetting?->ctaPhoneEntries()[0] ?? null,
            'contactPhones' => $siteSetting?->ctaPhoneEntries() ?? [],
            'contactFax' => $siteSetting?->fax(),
            'contactAddress' => $mainBranch?->address,
            'whatsappUrl' => $siteSetting?->ctaWhatsappEntries()[0]['url'] ?? null,
            'contactEmail' => $siteSetting?->support_email ?: $mainBranch?->email,
            'turnstileEnabled' => $siteSetting?->isTurnstileEnabled() ?? false,
            'turnstileSiteKey' => $siteSetting?->turnstile_site_key,
            'mainCategories' => $this->layoutData->mainCategories(),
            'productMenu' => $this->layoutData->productMenu(),
            'navbarProductCategories' => $this->layoutData->navbarProductCategories(),
            'cartCount' => $this->cart->count(),
        ]);
    }
}
