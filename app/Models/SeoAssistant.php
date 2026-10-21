<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoAssistant extends Model
{
    protected $fillable = [
        'home_meta_title_en', 'home_meta_title_ar',
        'home_meta_desc_en', 'home_meta_desc_ar',
        'home_meta_keywords_en', 'home_meta_keywords_ar',
        'home_meta_robots_en', 'home_meta_robots_ar',
        'about_meta_title_en', 'about_meta_title_ar',
        'about_meta_desc_en', 'about_meta_desc_ar',
        'about_meta_keywords_en', 'about_meta_keywords_ar',
        'about_meta_robots_en', 'about_meta_robots_ar',
        'contact_meta_title_en', 'contact_meta_title_ar',
        'contact_meta_desc_en', 'contact_meta_desc_ar',
        'contact_meta_keywords_en', 'contact_meta_keywords_ar',
        'contact_meta_robots_en', 'contact_meta_robots_ar',
        'blog_meta_title_en', 'blog_meta_title_ar',
        'blog_meta_desc_en', 'blog_meta_desc_ar',
        'blog_meta_keywords_en', 'blog_meta_keywords_ar',
        'blog_meta_robots_en', 'blog_meta_robots_ar',
        'service_meta_title_en', 'service_meta_title_ar',
        'service_meta_desc_en', 'service_meta_desc_ar',
        'service_meta_keywords_en', 'service_meta_keywords_ar',
        'service_meta_robots_en', 'service_meta_robots_ar',
        'products_meta_title_en', 'products_meta_title_ar',
        'products_meta_desc_en', 'products_meta_desc_ar',
        'products_meta_keywords_en', 'products_meta_keywords_ar',
        'products_meta_robots_en', 'products_meta_robots_ar',
        'projects_meta_title_en', 'projects_meta_title_ar',
        'projects_meta_desc_en', 'projects_meta_desc_ar',
        'projects_meta_keywords_en', 'projects_meta_keywords_ar',
        'projects_meta_robots_en', 'projects_meta_robots_ar',
        'gallery_meta_title_en', 'gallery_meta_title_ar',
        'gallery_meta_desc_en', 'gallery_meta_desc_ar',
        'gallery_meta_keywords_en', 'gallery_meta_keywords_ar',
        'gallery_meta_robots_en', 'gallery_meta_robots_ar',
        'branches_meta_title_en', 'branches_meta_title_ar',
        'branches_meta_desc_en', 'branches_meta_desc_ar',
        'branches_meta_keywords_en', 'branches_meta_keywords_ar',
        'branches_meta_robots_en', 'branches_meta_robots_ar',
        'google_analytics_id',
        'google_tag_manager_id',
        'google_search_console_verification',
        'sitemap_sections',
    ];

    protected $casts = [
        'sitemap_sections' => 'array',
    ];

    public function setGoogleSearchConsoleVerificationAttribute(?string $value): void
    {
        $this->attributes['google_search_console_verification'] = self::normalizeSearchConsoleVerification($value);
    }

    public static function normalizeSearchConsoleVerification(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Allow pasting the full meta tag from Google Search Console.
        if (preg_match('/content=["\']([^"\']+)["\']/i', $value, $matches) === 1) {
            return $matches[1];
        }

        return $value;
    }

    /**
     * Sections and static pages included in the public sitemap.
     *
     * @return array{
     *     static: bool,
     *     services: bool,
     *     blogs: bool,
     *     products: bool,
     *     projects: bool,
     *     pages: array<string, bool>
     * }
     */
    public function sitemapSections(): array
    {
        $defaults = self::defaultSitemapSections();
        $stored = is_array($this->sitemap_sections) ? $this->sitemap_sections : [];
        $pages = is_array($stored['pages'] ?? null) ? $stored['pages'] : [];

        return [
            'static' => (bool) ($stored['static'] ?? $defaults['static']),
            'services' => (bool) ($stored['services'] ?? $defaults['services']),
            'blogs' => (bool) ($stored['blogs'] ?? $defaults['blogs']),
            'products' => (bool) ($stored['products'] ?? $defaults['products']),
            'projects' => (bool) ($stored['projects'] ?? $defaults['projects']),
            'pages' => array_merge($defaults['pages'], array_map(fn (mixed $enabled): bool => (bool) $enabled, $pages)),
        ];
    }

    /**
     * @return array{
     *     static: bool,
     *     services: bool,
     *     blogs: bool,
     *     products: bool,
     *     projects: bool,
     *     pages: array<string, bool>
     * }
     */
    public static function defaultSitemapSections(): array
    {
        return [
            'static' => true,
            'services' => true,
            'blogs' => true,
            'products' => true,
            'projects' => true,
            'pages' => [
                'home' => true,
                'about' => true,
                'contact' => true,
                'services' => true,
                'news' => true,
                'products' => true,
                'projects' => true,
                'partners' => true,
            ],
        ];
    }
}
