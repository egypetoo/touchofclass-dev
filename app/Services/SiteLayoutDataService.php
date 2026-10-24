<?php

namespace App\Services;

use App\Enums\BranchTypeEnum;
use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SiteSocialLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SiteLayoutDataService
{
    /**
     * Cached site settings row with organization and active social links.
     */
    public function siteSetting(): ?SiteSetting
    {
        return Cache::remember('site_settings_layout', now()->addMinutes(5), function () {
            $with = [
                'organization',
                'socialLinks' => fn ($q) => $q
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ];

            return SiteSetting::query()
                ->with($with)
                ->whereNotNull('organization_id')
                ->orderBy('id')
                ->first()
                ?? SiteSetting::query()
                    ->with($with)
                    ->orderBy('id')
                    ->first();
        });
    }

    public function mainNavigation(): Collection
    {

        return Cache::remember('main_menu', now()->addMinutes(1), function () {
            return Menu::query()
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->isActive()->where('is_navbar', true)->orderBy('order')])
                ->isActive()
                ->where('is_navbar', true)
                ->orderBy('order')
                ->get();
        });
    }

    public function footerNavigation(): Collection
    {
        return Cache::remember('footer_navigation', now()->addMinutes(1), function () {
            return Menu::query()
                ->where('is_footer', true)
                ->isActive()
                ->orderBy('order')
                ->get(['id', 'title', 'route_name']);
        });
    }

    public function mainBranch(): ?Branch
    {
        return Cache::remember('main_branch', now()->addMinutes(1), function () {
            return Branch::query()
                ->isActive()
                ->type(BranchTypeEnum::Main)
                ->orderBy('order')
                ->first();
        });
    }

    public function branches(): Collection
    {
        return Cache::remember('branches', now()->addMinutes(1), function () {
            return Branch::query()
                ->isActive()
                ->orderBy('order')
                ->get();
        });
    }

    // public function breadcrumbImage(): ?Section
    // {
    //     return Cache::remember('breadcrumb_image', now()->addMinutes(5), function () {
    //         return Section::query()
    //             ->page(PageEnum::BREADCRUMB_IMAGE)
    //             ->type(SectionTypeEnum::BREADCRUMB_IMAGE)
    //             ->IsActive()
    //             ->orderBy('order')
    //             ->first();
    //     });
    // }
    public function services(): Collection
    {
        return Cache::remember('services', now()->addMinutes(5), function () {
            return Service::query()
                ->roots()
                ->isActive()
                ->orderBy('order')
                ->get(['id', 'title', 'slug', 'parent_id']);
        });
    }

    public function productMenu(): Collection
    {
        return Category::query()
            ->whereNull('parent_id')
            ->isActive()
            ->orderBy('order')
            ->with([
                'products' => fn ($query) => $query->active()->orderBy('sort_order')->with('media'),
                'children' => fn ($query) => $query->isActive()->orderBy('order')->with([
                    'products' => fn ($products) => $products->active()->orderBy('sort_order')->with('media'),
                ]),
            ])
            ->get();
    }

    /**
     * View-ready product dropdown rows for the site navbar.
     *
     * @return Collection<int, array{name: string, slug: string, url: string, count_label: string, products: list<array{name: string, url: string}>}>
     */
    public function navbarProductCategories(): Collection
    {
        return $this->productMenu()
            ->map(function (Category $category): array {
                $products = $category->products
                    ->concat($category->children->flatMap->products)
                    ->values();
                $count = $products->count();
                $slug = $category->getTranslation('slug', 'en');

                return [
                    'name' => $category->name,
                    'slug' => $slug,
                    'url' => route('products.index', ['category' => $slug]),
                    'count_label' => trans_choice('site.home_products_count', $count, ['count' => $count]),
                    'products' => $products
                        ->map(fn (Product $product): array => [
                            'name' => $product->name,
                            'url' => route('products.show', $product),
                        ])
                        ->all(),
                ];
            })
            ->values();
    }

    /**
     * @return array{
     *     home: string,
     *     logo: string,
     *     name: string,
     *     description: string,
     *     socials: list<array{url: string, label: string, icon: string, external: bool}>,
     *     projects: array{title: string, url: string, links: list<array{title: string, url: string}>},
     *     services: array{title: string, url: string, links: list<array{title: string, url: string}>},
     *     information: array{title: string, url: string, links: list<array{title: string, url: string}>},
     *     contact: array{title: string, url: string, items: list<array{icon: string, gold: bool, href: ?string, lines: list<string>, ltr: bool, external: bool}>},
     *     copyright: string,
     *     phone: ?array{href: string, label: string},
     *     whatsapp: ?array{href: string, label: string}
     * }
     */
    public function footer(?SiteSetting $setting, ?Branch $branch): array
    {
        $phone = $setting?->ctaPhoneEntries()[0] ?? $this->branchPhone($branch);
        $email = $setting?->support_email ?: $branch?->email;
        $phoneHref = $phone === null
            ? null
            : (str_starts_with($phone['tel'], 'tel:') ? $phone['tel'] : 'tel:'.$phone['tel']);
        $whatsappUrl = $setting?->ctaWhatsappEntries()[0]['url'] ?? null;
        $name = $setting?->siteName() ?: 'Touch of Class';
        $copyright = trim((string) $setting?->{'footer_copyright_'.app()->getLocale()});

        $addressLines = $this->textLines((string) ($branch?->address ?? ''));
        $hourLines = $this->textLines((string) ($branch?->working_hours ?? ''));
        $addressHref = filled($branch?->map_url) ? (string) $branch->map_url : null;

        $contactItems = array_values(array_filter([
            $phoneHref ? [
                'icon' => 'fa-solid fa-phone-volume',
                'gold' => true,
                'href' => $phoneHref,
                'lines' => [$phone['display']],
                'ltr' => true,
                'external' => false,
            ] : null,
            filled($email) ? [
                'icon' => 'fa-solid fa-envelope',
                'gold' => false,
                'href' => 'mailto:'.$email,
                'lines' => [(string) $email],
                'ltr' => true,
                'external' => false,
            ] : null,
            $addressLines !== [] ? [
                'icon' => 'fa-solid fa-location-dot',
                'gold' => false,
                'href' => $addressHref,
                'lines' => $addressLines,
                'ltr' => false,
                'external' => $addressHref !== null,
            ] : null,
            $hourLines !== [] ? [
                'icon' => 'fa-solid fa-clock-rotate-left',
                'gold' => false,
                'href' => null,
                'lines' => $hourLines,
                'ltr' => false,
                'external' => false,
            ] : null,
        ]));

        $projectLinks = ProjectCategory::query()
            ->isActive()
            ->orderBy('order')
            ->get()
            ->map(fn (ProjectCategory $category): array => [
                'title' => (string) $category->name,
                'url' => route('projects.index', ['category' => (string) $category->getTranslation('slug', 'en')]),
            ])
            ->all();

        $projectLinks[] = [
            'title' => (string) __('site.view_all_projects'),
            'url' => route('projects.index'),
        ];

        return [
            'home' => route('home'),
            'logo' => $setting?->logoUrl() ?? asset('assets/images/logo/logo-clear.png'),
            'name' => $name,
            'description' => trim(strip_tags((string) ($setting?->footerBody() ?: __('site.footer_default_desc')))),
            'socials' => $this->footerSocials($setting, $whatsappUrl),
            'projects' => [
                'title' => (string) __('site.projects'),
                'url' => route('projects.index'),
                'links' => $projectLinks,
            ],
            'services' => [
                'title' => (string) __('site.home_services_eyebrow'),
                'url' => route('services.index'),
                'links' => $this->services()
                    ->map(fn (Service $service): array => [
                        'title' => (string) $service->title,
                        'url' => route('services.show', $service),
                    ])
                    ->all(),
            ],
            'information' => [
                'title' => (string) __('site.information'),
                'url' => '',
                'links' => $this->footerNavigation()
                    ->map(fn (Menu $item): array => [
                        'title' => (string) $item->title,
                        'url' => $item->href(),
                    ])
                    ->all(),
            ],
            'contact' => [
                'title' => (string) __('site.contact_us'),
                'url' => route('contact-us'),
                'items' => $contactItems,
            ],
            'copyright' => $copyright !== ''
                ? $copyright
                : '© '.date('Y').' '.$name.'. '.__('site.all_rights_reserved'),
            'phone' => $phoneHref === null ? null : [
                'href' => $phoneHref,
                'label' => (string) __('site.home_contact_eyebrow'),
            ],
            'whatsapp' => filled($whatsappUrl) ? [
                'href' => $whatsappUrl,
                'label' => (string) __('site.whatsapp'),
            ] : null,
        ];
    }

    /**
     * @return list<array{url: string, label: string, icon: string, external: bool}>
     */
    private function footerSocials(?SiteSetting $setting, ?string $whatsappUrl): array
    {
        $socials = ($setting?->socialLinks ?? collect())
            ->filter(fn (SiteSocialLink $link): bool => filled($link->url))
            ->map(fn (SiteSocialLink $link): array => [
                'url' => (string) $link->url,
                'label' => (string) ($link->platformEnum()?->getLabel() ?? ''),
                'icon' => $link->platformEnum()?->iconClass() ?? 'fa-solid fa-globe',
                'external' => str_starts_with((string) $link->url, 'http'),
            ])
            ->values()
            ->all();

        if (filled($whatsappUrl)) {
            $socials[] = [
                'url' => $whatsappUrl,
                'label' => (string) __('site.whatsapp'),
                'icon' => 'fa-brands fa-whatsapp',
                'external' => true,
            ];
        }

        return $socials;
    }

    /**
     * @return array{display: string, tel: string}|null
     */
    private function branchPhone(?Branch $branch): ?array
    {
        if ($branch === null || blank($branch->phone_1)) {
            return null;
        }

        $code = preg_replace('/\D+/', '', (string) $branch->phone_1_country_code) ?? '';
        $number = ltrim(preg_replace('/\D+/', '', (string) $branch->phone_1) ?? '', '0');

        if ($number === '') {
            return null;
        }

        return [
            'display' => $code !== '' ? '+'.$code.' '.$number : $number,
            'tel' => '+'.$code.$number,
        ];
    }

    /**
     * @return list<string>
     */
    private function textLines(string $value): array
    {
        $value = trim(strip_tags($value));

        if ($value === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/u', $value) ?: [];
        $lines = array_values(array_filter(array_map(trim(...), $lines), fn (string $line): bool => $line !== ''));

        if (count($lines) === 1 && preg_match('/^(.+?)[،,]\s*(.+)$/u', $lines[0], $matches) === 1) {
            return [trim($matches[1]), trim($matches[2])];
        }

        return $lines;
    }

    public function mainCategories(): Collection
    {
        return Cache::remember('main_categories', now()->addMinutes(5), function () {
            return Category::query()
                ->whereNull('parent_id')
                ->isActive()
                ->orderBy('order')
                ->get(['id', 'name', 'slug', 'parent_id']);
        });
    }
}
