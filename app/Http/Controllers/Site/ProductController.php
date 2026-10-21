<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductListingRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(ProductListingRequest $request): View
    {
        $data['products_breadcrumb'] = Section::query()
            ->with('media')
            ->page(PageEnum::PRODUCTS)
            ->type(SectionTypeEnum::BREADCRUMB)
            ->isActive()
            ->first();

        $data['filterCategories'] = Category::query()
            ->whereNull('parent_id')
            ->isActive()
            ->orderBy('order')
            ->with('children.children')
            ->get()
            ->each(function (Category $category): void {
                $category->setAttribute(
                    'products_count',
                    Product::query()
                        ->active()
                        ->whereIn('category_id', $category->branchIds())
                        ->count()
                );
            });

        $data['allProductsCount'] = Product::query()->active()->count();

        $category = $this->listingCategory($request);
        $data['activeCategoryId'] = $category?->id;
        $data['activeCategorySlug'] = $category?->getTranslation('slug', 'en');
        $restrictToCategory = $request->filled('category') || $request->filled('category_id');

        $applyCategory = function (Builder $query) use ($restrictToCategory, $category): void {
            if (! $restrictToCategory) {
                return;
            }

            if ($category) {
                $query->whereIn('category_id', $category->branchIds());

                return;
            }

            $query->whereKey([]);
        };

        $brandsQuery = Product::query()->active()->orderBy('sort_order');
        $applyCategory($brandsQuery);

        $data['brands'] = $brandsQuery
            ->get()
            ->groupBy(fn (Product $product): string => $product->getTranslation('name', 'en'))
            ->map(fn ($group): array => [
                'value' => $group->first()->getTranslation('name', 'en'),
                'label' => $group->first()->name,
                'count' => $group->count(),
            ])
            ->values();

        $productsQuery = Product::query()
            ->active()
            ->with(Product::listingRelations());

        $applyCategory($productsQuery);

        $brands = collect($request->input('brand', []))
            ->filter(fn (mixed $brand): bool => is_string($brand) && $brand !== '')
            ->values();

        if ($brands->isNotEmpty()) {
            $productsQuery->where(function ($query) use ($brands): void {
                $query->whereIn('name->en', $brands)
                    ->orWhereIn('name->ar', $brands);
            });
        }

        if ($request->filled('q')) {
            $search = '%'.$request->string('q')->toString().'%';
            $productsQuery->where(function ($query) use ($search): void {
                $query->where('name->en', 'like', $search)
                    ->orWhere('name->ar', 'like', $search)
                    ->orWhere('code', 'like', $search)
                    ->orWhere('short_description->en', 'like', $search)
                    ->orWhere('short_description->ar', 'like', $search);
            });
        }

        $locale = app()->getLocale();

        match ($request->string('sort')->toString()) {
            'name', 'name_asc' => $productsQuery->orderBy("name->{$locale}"),
            'name_desc' => $productsQuery->orderByDesc("name->{$locale}"),
            'price_asc' => $productsQuery->orderBy('price'),
            'price_desc' => $productsQuery->orderByDesc('price'),
            default => $productsQuery->orderBy('sort_order')->latest('id'),
        };

        $data['products'] = $productsQuery->paginate(12)->withQueryString();

        return view('site.products.index', $data);
    }

    public function show(Product $product): View|RedirectResponse
    {
        if ($redirect = $product->redirectIfSlugLocaleMismatch()) {
            return $redirect;
        }

        $product->load([
            'category',
            'variants.attributeValues',
            'tabs' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id'),
            'tags' => fn ($q) => $q->where('is_active', true),
            'discounts' => fn ($q) => $q->active(),
            'media' => fn ($q) => $q->where('collection_name', 'products')->orderBy('order_column'),
        ]);

        $relatedProducts = Product::query()
            ->active()
            ->whereKeyNot($product->getKey())
            ->with(Product::listingRelations())
            ->when($product->category_id, fn ($query) => $query->orderByRaw('category_id = ? desc', [$product->category_id]))
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $productsBreadcrumb = Section::query()
            ->with('media')
            ->page(PageEnum::PRODUCTS)
            ->type(SectionTypeEnum::BREADCRUMB)
            ->isActive()
            ->first();

        return view('site.products.show', compact('product', 'relatedProducts', 'productsBreadcrumb'));
    }

    private function listingCategory(ProductListingRequest $request): ?Category
    {
        if ($request->filled('category')) {
            $slug = match ($request->string('category')->toString()) {
                'motors' => 'deep-well-motors',
                'pumps' => 'deep-well-pumps',
                'pipes' => 'deep-well-pipes',
                'cables' => 'submersible-cables',
                default => $request->string('category')->toString(),
            };

            return Category::query()
                ->isActive()
                ->where(function ($query) use ($slug): void {
                    $query->where('slug->en', $slug)
                        ->orWhere('slug->ar', $slug);
                })
                ->first();
        }

        if (! $request->filled('category_id')) {
            return null;
        }

        return Category::query()->isActive()->find($request->integer('category_id'));
    }
}
