<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Services\Seo\SeoService;
use App\Support\LocaleUrl;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoComposer
{
    public function __construct(
        protected SeoService $seoService,
        protected Request $request,
    ) {}

    public function compose(View $view): void
    {
        if ($details = $this->resolveDetailsPageSeo()) {
            [$schema, $metatags, $faqSchema] = array_pad($details, 3, null);
        } else {
            [$schema, $metatags] = $this->seoService->get($this->detectCurrentPage());
            $faqSchema = null;
        }

        $metatags = $this->syncMetatagsWithCurrentRequestUrl($metatags);

        $view->with('metatags', $metatags);
        $view->with('schema', $schema);

        if (! empty($faqSchema)) {
            $view->with('faq_schema', $faqSchema);
        }
    }

    protected function syncMetatagsWithCurrentRequestUrl(array $metatags): array
    {
        $url = url()->current().LocaleUrl::indexableQueryString();
        $metatags['canonical'] = $url;
        $metatags['og_url'] = $url;
        $metatags['twitter_url'] = $url;

        return $metatags;
    }

    protected function resolveDetailsPageSeo(): ?array
    {
        $path = $this->pathWithoutLocale();

        if ($path === 'products' && $this->request->filled('category_id')) {
            $category = Category::query()->isActive()->find($this->request->integer('category_id'));
            if ($category) {
                [$schema, $metatags] = $this->seoService->categoryDetailsPage($category);
                $schema['url'] = url()->current().LocaleUrl::indexableQueryString();

                return [$schema, $metatags];
            }
        }

        if (preg_match('#^services/(.+)$#', $path, $matches)) {
            $service = $this->findBySlug(Service::query()->isActive(), $matches[1]);
            if ($service) {
                return $this->seoService->serviceDetailsPage($service);
            }
        }

        if (preg_match('#^products/(.+)$#', $path, $matches)) {
            $product = $this->findBySlug(Product::query()->active(), $matches[1]);
            if ($product) {
                return $this->seoService->productDetailsPage($product);
            }
        }

        if (preg_match('#^projects/(.+)$#', $path, $matches)) {
            $project = $this->findBySlug(Project::query()->isActive(), $matches[1]);
            if ($project) {
                return $this->seoService->projectDetailsPage($project);
            }
        }

        if (preg_match('#^categories/(.+)$#', $path, $matches)) {
            $category = $this->findBySlug(Category::query()->isActive(), $matches[1]);
            if ($category) {
                return $this->seoService->categoryDetailsPage($category);
            }
        }

        return null;
    }

    protected function findBySlug($query, string $slug): mixed
    {
        $locale = app()->getLocale();
        $slug = urldecode($slug);

        $record = (clone $query)->where("slug->{$locale}", $slug)->first();
        if ($record) {
            return $record;
        }

        foreach (['en', 'ar'] as $lang) {
            $record = (clone $query)->where("slug->{$lang}", $slug)->first();
            if ($record) {
                return $record;
            }
        }

        return null;
    }

    protected function detectCurrentPage(): string
    {
        return match ($this->pathWithoutLocale()) {
            '', 'home' => 'home',
            'about-us' => 'about',
            'contact-us' => 'contact',
            'services' => 'services',
            'products' => 'products',
            'projects' => 'projects',
            default => 'home',
        };
    }

    protected function pathWithoutLocale(): string
    {
        $path = $this->request->path();
        $locale = app()->getLocale();

        if (str_starts_with($path, $locale)) {
            $path = substr($path, strlen($locale) + 1);
        }

        return rtrim($path, '/');
    }
}
