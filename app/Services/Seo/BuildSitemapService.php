<?php

namespace App\Services\Seo;

use App\Models\Blog;
use App\Models\Product;
use App\Models\Project;
use App\Models\SeoAssistant;
use App\Models\Service;
use App\Support\LocaleUrl;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class BuildSitemapService
{
    /** @var array<int, string> */
    protected array $locales = [];

    /**
     * @var array{
     *     static: bool,
     *     services: bool,
     *     blogs: bool,
     *     products: bool,
     *     projects: bool,
     *     pages: array<string, bool>
     * }
     */
    protected array $sections = [];

    public function generateSitemap(): void
    {
        $this->locales = array_keys(config('laravellocalization.supportedLocales', ['en' => [], 'ar' => []]));
        $this->sections = SeoAssistant::query()->first()?->sitemapSections()
            ?? SeoAssistant::defaultSitemapSections();

        $dir = storage_path('app/public/sitemap');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($this->locales as $locale) {
            app()->setLocale($locale);

            $this->writeOrRemoveSection($locale, 'services', fn () => $this->buildIndex(
                Service::query()->isActive()->get(),
                "sitemap_services_{$locale}.xml",
                $locale,
                'services',
            ));
            $this->writeOrRemoveSection($locale, 'blogs', fn () => $this->buildIndex(
                Blog::query()->isActive()->get(),
                "sitemap_blogs_{$locale}.xml",
                $locale,
                'news',
            ));
            $this->writeOrRemoveSection($locale, 'products', fn () => $this->buildIndex(
                Product::query()->active()->get(),
                "sitemap_products_{$locale}.xml",
                $locale,
                'products',
            ));
            $this->writeOrRemoveSection($locale, 'projects', fn () => $this->buildIndex(
                Project::query()->isActive()->get(),
                "sitemap_projects_{$locale}.xml",
                $locale,
                'projects',
            ));
            $this->writeStaticSitemap($locale, $dir);
        }

        foreach (['ar', 'en'] as $locale) {
            if (in_array($locale, $this->locales, true)) {
                continue;
            }

            foreach (['static', 'services', 'blogs', 'products', 'projects'] as $type) {
                $this->deleteSitemapFile("sitemap_{$type}_{$locale}.xml");
            }
        }

        $this->generateSitemapIndex();
        $this->generateRobotsTxt();
    }

    protected function writeOrRemoveSection(string $locale, string $type, callable $writer): void
    {
        $file = "sitemap_{$type}_{$locale}.xml";

        if (! $this->sectionEnabled($type)) {
            $this->deleteSitemapFile($file);

            return;
        }

        $writer();
    }

    protected function writeStaticSitemap(string $locale, string $dir): void
    {
        $file = "sitemap_static_{$locale}.xml";

        if (! $this->sectionEnabled('static')) {
            $this->deleteSitemapFile($file);

            return;
        }

        $pages = array_values(array_filter(
            $this->staticPageDefinitions(),
            fn (array $page): bool => $this->pageEnabled($page['key']),
        ));

        if ($pages === []) {
            $this->deleteSitemapFile($file);

            return;
        }

        $staticSitemap = Sitemap::create();

        foreach ($pages as $page) {
            $staticSitemap->add(
                Url::create($this->localizedUrl($locale, $page['path']))
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['frequency'])
            );
        }

        $tempPath = "{$dir}/{$file}";
        $staticSitemap->writeToFile($tempPath);
        copy($tempPath, public_path($file));
    }

    /**
     * @return list<array{key: string, path: string, priority: float, frequency: string}>
     */
    protected function staticPageDefinitions(): array
    {
        return [
            ['key' => 'home', 'path' => '/', 'priority' => 1.0, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['key' => 'about', 'path' => '/about-us', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['key' => 'contact', 'path' => '/contact-us', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['key' => 'services', 'path' => '/services', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['key' => 'news', 'path' => '/news', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['key' => 'products', 'path' => '/products', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['key' => 'projects', 'path' => '/projects', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['key' => 'partners', 'path' => '/partners', 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];
    }

    protected function sectionEnabled(string $type): bool
    {
        return (bool) ($this->sections[$type] ?? false);
    }

    protected function pageEnabled(string $key): bool
    {
        return (bool) ($this->sections['pages'][$key] ?? false);
    }

    protected function deleteSitemapFile(string $file): void
    {
        $paths = [
            public_path($file),
            storage_path("app/public/sitemap/{$file}"),
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    protected function buildIndex($collection, string $filePath, string $locale, string $prefix): void
    {
        $sitemap = Sitemap::create();

        foreach ($collection as $item) {
            $slug = $item->getTranslation('slug', $locale);
            if (! filled($slug)) {
                continue;
            }

            $sitemap->add(
                Url::create($this->localizedUrl($locale, "/{$prefix}/{$slug}"))
                    ->setLastModificationDate($item->updated_at)
                    ->setPriority(0.8)
            );
        }

        try {
            $tempPath = storage_path("app/public/sitemap/{$filePath}");
            $sitemap->writeToFile($tempPath);
            copy($tempPath, public_path($filePath));
        } catch (\Throwable $e) {
            Log::error('Failed to write sitemap file', [
                'error' => $e->getMessage(),
                'file' => $filePath,
            ]);
        }
    }

    protected function localizedUrl(string $locale, string $path): string
    {
        return LocaleUrl::toLocalePath($locale, $path);
    }

    protected function generateSitemapIndex(): void
    {
        $sitemapIndex = SitemapIndex::create();
        $types = ['static', 'services', 'blogs', 'products', 'projects'];

        foreach ($this->locales as $locale) {
            foreach ($types as $type) {
                if (! $this->sectionEnabled($type)) {
                    continue;
                }

                $file = "sitemap_{$type}_{$locale}.xml";
                if (! is_file(public_path($file))) {
                    continue;
                }

                $sitemapIndex->add(
                    \Spatie\Sitemap\Tags\Sitemap::create(url($file))->setLastModificationDate(now())
                );
            }
        }

        $tempPath = storage_path('app/public/sitemap/sitemap.xml');
        $sitemapIndex->writeToFile($tempPath);
        copy($tempPath, public_path('sitemap.xml'));
    }

    protected function generateRobotsTxt(): void
    {
        $baseUrl = rtrim(url('/'), '/');
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "\n";
        $content .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        file_put_contents(public_path('robots.txt'), $content);
    }
}
