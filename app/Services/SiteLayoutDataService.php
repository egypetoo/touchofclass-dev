<?php

namespace App\Services;

use App\Enums\BranchTypeEnum;
use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
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
