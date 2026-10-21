<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Admins\AdminResource;
use App\Filament\Resources\Sections\SectionResource;
use App\Http\Middleware\SetAdminLocale;
use App\Models\Organization;
use App\Models\SiteSetting;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        Resource::scopeToTenant(true);

        return $panel
            ->default()
            // ->tenant(Organization::class, slugAttribute: 'id')
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login()
            ->brandName(fn (): string => $this->resolveBrandName())
            ->brandLogo(fn (): HtmlString => $this->resolveBrandLogoHtml())
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationGroups([
                SectionResource::NAVIGATION_GROUP_KEY => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.sections_management'))
                    ->icon(Heroicon::OutlinedRectangleStack)
                    ->collapsible(),
                'Company' => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.company_nav_group'))
                    ->collapsible(),
                'Blog' => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.blog_nav_group'))
                    ->collapsible(),
                'Media' => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.media_nav_group'))
                    ->collapsible(),
                'categories' => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.categories_management'))
                    ->collapsible(),
                'Products' => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.products_nav_group'))
                    ->collapsible(),
                'site' => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.site_nav_group'))
                    ->collapsible(),
                // Keep User Management last in the sidebar.
                AdminResource::NAVIGATION_GROUP_KEY => NavigationGroup::make()
                    ->label(fn (): string => __('admin_trans.access_management'))
                    ->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->middleware([
                SetAdminLocale::class,
            ], isPersistent: true)
            ->userMenuItems([
                Action::make('switchToArabic')
                    ->label('العربية')
                    ->icon(Heroicon::OutlinedLanguage)
                    ->url(fn (): string => route('admin.locale', 'ar'))
                    ->visible(fn (): bool => app()->getLocale() !== 'ar'),
                Action::make('switchToEnglish')
                    ->label('English')
                    ->icon(Heroicon::OutlinedLanguage)
                    ->url(fn (): string => route('admin.locale', 'en'))
                    ->visible(fn (): bool => app()->getLocale() !== 'en'),
            ])
            ->plugins([
                SpatieTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ar'])
                    ->persist(),
                FilamentShieldPlugin::make()
                    ->navigationGroup(AdminResource::NAVIGATION_GROUP_KEY)
                    ->navigationSort(999),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->tenantMiddleware([
                // ApplyTenantScopes::class,
            ], isPersistent: true);
    }

    protected function resolveBrandName(): string
    {
        $setting = $this->resolveSiteSetting();

        if ($setting !== null) {
            $name = $setting->{'site_name_'.app()->getLocale()}
                ?: $setting->site_name_en
                ?: $setting->site_name_ar;

            if (filled($name)) {
                return (string) $name;
            }
        }

        $organization = Organization::query()->orderBy('created_at')->first();
        if ($organization !== null) {
            $orgName = $organization->{'name_'.app()->getLocale()}
                ?: $organization->name_en
                ?: $organization->name_ar;

            if (filled($orgName)) {
                return (string) $orgName;
            }
        }

        return (string) config('app.name');
    }

    protected function resolveBrandLogoHtml(): HtmlString
    {
        $siteUrl = url('/');
        $name = e($this->resolveBrandName());
        $logoUrl = $this->resolveLogoUrl();

        if ($logoUrl !== null) {
            return new HtmlString(
                '<a href="'.e($siteUrl).'" target="_blank" rel="noopener noreferrer" title="'.$name.'" style="display:inline-flex;align-items:center;height:2.5rem;max-width:11rem">'
                .'<img src="'.e($logoUrl).'" alt="'.$name.'" style="height:2.5rem;width:auto;max-width:11rem;object-fit:contain" />'
                .'</a>'
            );
        }

        return new HtmlString(
            '<a href="'.e($siteUrl).'" target="_blank" rel="noopener noreferrer" class="fi-logo" style="font-weight:700;font-size:1rem;line-height:1.2;white-space:nowrap">'
            .$name
            .'</a>'
        );
    }

    protected function resolveLogoUrl(): ?string
    {
        $setting = $this->resolveSiteSetting();
        if ($setting === null) {
            return null;
        }

        $path = $setting->preferredLogoPath();

        return $setting->publicStorageUrl($path);
    }

    protected function resolveSiteSetting(): ?SiteSetting
    {
        static $setting = false;

        if ($setting === false) {
            try {
                $organization = Organization::query()->orderBy('created_at')->first();

                $setting = SiteSetting::query()
                    ->when(
                        $organization !== null,
                        fn ($query) => $query->where('organization_id', $organization->id),
                        fn ($query) => $query->whereNotNull('organization_id'),
                    )
                    ->orderBy('id')
                    ->first()
                    ?? SiteSetting::query()->orderByDesc('id')->first();
            } catch (\Throwable) {
                $setting = null;
            }
        }

        return $setting instanceof SiteSetting ? $setting : null;
    }
}
