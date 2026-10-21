<?php

namespace App\Services\Seo;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Project;
use App\Models\SeoAssistant;
use App\Models\Service;
use App\Models\SiteSetting;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SeoService
{
    protected string $lang;

    protected ?SeoAssistant $seo;

    protected ?SiteSetting $siteSetting;

    public function __construct()
    {
        $this->lang = LaravelLocalization::getCurrentLocale();
        $this->seo = SeoAssistant::query()->first();
        $this->siteSetting = SiteSetting::query()->with(['socialLinks', 'organization'])->first();
    }

    public function get(string $page = 'home'): array
    {
        return match ($page) {
            'about' => $this->aboutPage(),
            'contact' => $this->contactPage(),
            'services' => $this->servicesPage(),
            'products' => $this->productsPage(),
            'projects' => $this->projectsPage(),
            'noindex' => $this->noIndexPage(),
            default => $this->homePage(),
        };
    }

    public function homePage(): array
    {
        $title = $this->pageTitle($this->pageField('home', 'title', $this->lang === 'ar' ? 'الرئيسية' : 'Home'));
        $desc = $this->resolveDescription(
            $this->pageField('home', 'desc'),
            $this->defaultSiteDescription()
        );
        $url = $this->localizedPath('');
        $image = $this->defaultImage();

        return [
            $this->generateMedicalClinicSchema($desc, $url, $image),
            $this->generateMetaTags($title, $desc, $url, $image, keywords: $this->pageField('home', 'keywords')),
        ];
    }

    public function aboutPage(): array
    {
        $title = $this->pageTitle($this->pageField('about', 'title', $this->lang === 'ar' ? 'من نحن' : 'About Us'));
        $desc = $this->resolveDescription($this->pageField('about', 'desc'), $this->defaultAboutDescription());
        $url = $this->localizedPath('about-us');
        $image = $this->defaultImage();

        return [
            $this->generateMedicalClinicSchema($desc, $url, $image),
            $this->generateMetaTags($title, $desc, $url, $image, keywords: $this->pageField('about', 'keywords')),
        ];
    }

    public function contactPage(): array
    {
        $title = $this->pageTitle($this->pageField('contact', 'title', $this->lang === 'ar' ? 'اتصل بنا' : 'Contact Us'));
        $desc = $this->resolveDescription($this->pageField('contact', 'desc'), $this->defaultContactDescription());
        $url = $this->localizedPath('contact-us');
        $image = $this->defaultImage();

        return [
            $this->generateMedicalClinicSchema($desc, $url, $image),
            $this->generateMetaTags($title, $desc, $url, $image, keywords: $this->pageField('contact', 'keywords')),
        ];
    }

    public function servicesPage(): array
    {
        $title = $this->pageTitle($this->pageField('service', 'title', $this->lang === 'ar' ? 'خدماتنا' : 'Our Services'));
        $desc = $this->resolveDescription($this->pageField('service', 'desc'), $this->defaultServicesListDescription());
        $url = $this->localizedPath('services');
        $image = $this->defaultImage();

        return [
            $this->generateListSchema('Service', $title, $desc, $url, $image),
            $this->generateMetaTags($title, $desc, $url, $image, keywords: $this->pageField('service', 'keywords')),
        ];
    }

    public function productsPage(): array
    {
        $title = $this->pageTitle($this->pageField('products', 'title', $this->lang === 'ar' ? 'منتجاتنا' : 'Our Products'));
        $desc = $this->resolveDescription($this->pageField('products', 'desc'), $this->defaultProductsListDescription());
        $url = $this->localizedPath('products');
        $image = $this->defaultImage();

        return [
            $this->generateListSchema('ItemList', $title, $desc, $url, $image),
            $this->generateMetaTags($title, $desc, $url, $image, keywords: $this->pageField('products', 'keywords')),
        ];
    }

    public function projectsPage(): array
    {
        $title = $this->pageTitle($this->pageField('projects', 'title', $this->lang === 'ar' ? 'مشاريعنا' : 'Our Projects'));
        $desc = $this->resolveDescription(
            $this->pageField('projects', 'desc'),
            $this->lang === 'ar' ? 'استعرض مشاريعنا وأعمالنا.' : 'Browse our projects and portfolio.'
        );
        $url = $this->localizedPath('projects');
        $image = $this->defaultImage();

        return [
            $this->generateListSchema('ItemList', $title, $desc, $url, $image),
            $this->generateMetaTags($title, $desc, $url, $image, keywords: $this->pageField('projects', 'keywords')),
        ];
    }

    public function noIndexPage(): array
    {
        $title = $this->pageTitle($this->brandName());
        $desc = $this->defaultSiteDescription();
        $url = url()->current();
        $image = $this->defaultImage();

        $metatags = $this->generateMetaTags($title, $desc, $url, $image);
        $metatags['robots'] = 'noindex, nofollow';

        return [null, $metatags];
    }

    public function serviceDetailsPage(Service $service): array
    {
        [$pageName, $title, $desc] = $this->resolveEntitySeo(
            $service,
            (string) $service->getTranslation('title', $this->lang),
            fn (string $name) => $this->defaultServiceDescription($name),
            strip_tags((string) ($service->getTranslation('short_text', $this->lang) ?? ''))
        );

        $url = $service->getLocalizedUrl($this->lang) ?: url()->current();
        $image = $service->mainImageUrl() ?: $this->defaultImage();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $pageName,
            'description' => $desc,
            'image' => $image,
            'url' => $url,
            'provider' => [
                '@type' => 'MedicalClinic',
                'name' => $this->brandName(),
            ],
        ];

        return [$schema, $this->generateMetaTags($title, $desc, $url, $image)];
    }

    public function productDetailsPage(Product $product): array
    {
        [$pageName, $title, $desc] = $this->resolveEntitySeo(
            $product,
            (string) $product->getTranslation('name', $this->lang),
            fn (string $name) => $this->defaultProductDescription($name),
            strip_tags((string) ($product->getTranslation('short_description', $this->lang) ?? ''))
        );

        $url = $product->getLocalizedUrl($this->lang) ?: url()->current();
        $image = $product->mainImageUrl() ?: $this->defaultImage();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $pageName,
            'description' => $desc,
            'image' => $image,
            'url' => $url,
            'brand' => [
                '@type' => 'Brand',
                'name' => $this->brandName(),
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $product->price,
                'priceCurrency' => 'EGP',
                'availability' => $product->hasOrderableStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => $url,
            ],
        ];

        return [$schema, $this->generateMetaTags($title, $desc, $url, $image)];
    }

    public function projectDetailsPage(Project $project): array
    {
        [$pageName, $title, $desc] = $this->resolveEntitySeo(
            $project,
            (string) $project->getTranslation('title', $this->lang),
            fn (string $name) => $this->lang === 'ar'
                ? "تفاصيل مشروع {$name}."
                : "Details about the {$name} project.",
            strip_tags((string) ($project->getTranslation('short_text', $this->lang) ?? ''))
        );

        $url = $project->getLocalizedUrl($this->lang) ?: url()->current();
        $image = $project->coverUrl() ?: $this->defaultImage();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $pageName,
            'description' => $desc,
            'image' => $image,
            'url' => $url,
        ];

        return [$schema, $this->generateMetaTags($title, $desc, $url, $image)];
    }

    public function categoryDetailsPage($category): array
    {
        [$pageName, $title, $desc] = $this->resolveEntitySeo(
            $category,
            (string) $category->getTranslation('name', $this->lang),
            fn (string $name) => $this->defaultCategoryDescription($name),
            strip_tags((string) ($category->getTranslation('short_text', $this->lang) ?? ''))
        );

        $url = $category->getLocalizedUrl($this->lang) ?: url()->current();
        $image = $category->getFirstMediaUrl('categories') ?: $this->defaultImage();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $pageName,
            'description' => $desc,
            'url' => $url,
            'image' => $image,
        ];

        return [$schema, $this->generateMetaTags($title, $desc, $url, $image)];
    }

    public function generateMetaTags(
        string $title,
        string $description,
        string $url,
        string $image,
        ?string $author = null,
        ?string $publishedAt = null,
        ?string $updatedAt = null,
        ?string $keywords = null,
    ): array {
        $description = $this->resolveDescription($description, $this->defaultSiteDescription());
        $twitter = $this->resolveTwitterHandle();

        return [
            'charset' => 'utf-8',
            'viewport' => 'width=device-width, initial-scale=1.0',
            'language' => $this->lang,
            'robots' => 'index, follow',
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'author' => $author ?? $this->brandName(),
            'canonical' => $url,
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $url,
            'og_image' => $image,
            'og_type' => 'website',
            'og_site_name' => $this->brandName(),
            'og_locale' => $this->lang,
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => $image,
            'twitter_site' => $twitter,
            'twitter_creator' => $twitter,
            'article_published_time' => $publishedAt,
            'article_modified_time' => $updatedAt,
            'google_analytics_id' => $this->seo?->google_analytics_id,
            'google_tag_manager_id' => $this->seo?->google_tag_manager_id,
            'google_search_console_verification' => $this->seo?->google_search_console_verification,
        ];
    }

    protected function resolveEntitySeo(object $entity, string $defaultPageName, callable $defaultDescFallback, string $shortText = ''): array
    {
        $metaTitle = method_exists($entity, 'getTranslation')
            ? trim((string) $entity->getTranslation('meta_title', $this->lang))
            : '';

        $pageName = $metaTitle !== '' ? $metaTitle : trim(strip_tags($defaultPageName));
        $fullTitle = $this->buildFullTitle($pageName);

        $metaDesc = method_exists($entity, 'getTranslation')
            ? (string) $entity->getTranslation('meta_desc', $this->lang)
            : '';

        $desc = $this->resolveDescription(
            $metaDesc !== '' ? $metaDesc : $shortText,
            $defaultDescFallback($pageName)
        );

        return [$pageName, $fullTitle, $desc];
    }

    protected function pageField(string $page, string $field, string $fallback = ''): string
    {
        $key = "{$page}_meta_{$field}_{$this->lang}";
        $value = trim((string) ($this->seo?->{$key} ?? ''));

        return $value !== '' ? $value : $fallback;
    }

    protected function buildFullTitle(string $pageTitle): string
    {
        $pageTitle = trim(strip_tags($pageTitle));
        $brand = $this->brandName();

        if ($pageTitle === '') {
            return $brand;
        }

        if (stripos($pageTitle, $brand) !== false) {
            return $pageTitle;
        }

        return $pageTitle.' | '.$brand;
    }

    protected function pageTitle(string $title): string
    {
        return $this->buildFullTitle($title);
    }

    protected function brandName(): string
    {
        $name = trim((string) ($this->siteSetting?->{'site_name_'.$this->lang} ?? ''));

        if ($name === '') {
            $name = trim((string) ($this->siteSetting?->siteName() ?? ''));
        }

        if ($name === '') {
            $org = $this->siteSetting?->organization;
            $name = trim((string) (
                $this->lang === 'ar'
                    ? ($org?->name_ar ?: $org?->name_en)
                    : ($org?->name_en ?: $org?->name_ar)
            ));
        }

        if ($name === '') {
            $name = $this->lang === 'ar' ? 'كينج موتورز' : 'King Motors';
        }

        return $name;
    }

    protected function defaultImage(): string
    {
        $logo = $this->siteSetting?->logoUrl();

        return filled($logo) ? $logo : asset('assets/images/logo/logo-transparent.webp');
    }

    protected function localizedPath(string $path): string
    {
        $path = ltrim($path, '/');

        return $path === ''
            ? url('/'.$this->lang)
            : url('/'.$this->lang.'/'.$path);
    }

    protected function resolveDescription(?string $description, string $fallback = ''): string
    {
        $desc = trim(strip_tags(html_entity_decode((string) $description, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return $desc !== '' ? $desc : trim(strip_tags($fallback));
    }

    protected function defaultSiteDescription(): string
    {
        return $this->lang === 'ar'
            ? 'كينج موتورز — مواتير ومضخات ومواسير وكابلات لحلول ضخ المياه المتكاملة.'
            : 'King Motors — motors, pumps, pipes and cables for integrated water pumping solutions.';
    }

    protected function defaultAboutDescription(): string
    {
        return $this->lang === 'ar'
            ? 'تعرف على كينج موتورز ومعدات ضخ المياه للزراعة والصناعة والمشروعات.'
            : 'Learn about King Motors and our water pumping equipment.';
    }

    protected function defaultContactDescription(): string
    {
        return $this->lang === 'ar'
            ? 'تواصل مع كينج موتورز لطلب عرض سعر أو اختيار طلمبة أو الدعم الفني.'
            : 'Contact King Motors for quotations, pump selection and technical support.';
    }

    protected function defaultServicesListDescription(): string
    {
        return $this->lang === 'ar'
            ? 'استكشف خدمات كينج موتورز: استشارات وقطع غيار وصيانة بالضمان وتوريد وتركيب.'
            : 'Explore King Motors services: consulting, spare parts, warranty maintenance, and supply and installation.';
    }

    protected function defaultProductsListDescription(): string
    {
        return $this->lang === 'ar'
            ? 'تصفح منتجات كينج موتورز: المواتير والمضخات والمواسير والكابلات.'
            : 'Browse King Motors products: motors, pumps, pipes and cables.';
    }

    protected function defaultServiceDescription(string $serviceName): string
    {
        return $this->lang === 'ar'
            ? "{$serviceName} من كينج موتورز — دعم فني لحلول ضخ المياه."
            : "{$serviceName} by King Motors — technical support for water pumping solutions.";
    }

    protected function defaultProductDescription(string $productName): string
    {
        return $this->lang === 'ar'
            ? "{$productName} — متاح لدى كينج موتورز."
            : "{$productName} — available from King Motors.";
    }

    protected function defaultCategoryDescription(string $categoryName): string
    {
        return $this->lang === 'ar'
            ? "تصفح {$categoryName} — منتجات كينج موتورز."
            : "Browse {$categoryName} — products from King Motors.";
    }

    protected function resolveTwitterHandle(): ?string
    {
        $twitter = $this->siteSetting?->socialLinks
            ?->firstWhere('platform', 'x')
            ?->url;

        if (! filled($twitter)) {
            return null;
        }

        if (preg_match('/^@[\w]{1,15}$/', $twitter)) {
            return $twitter;
        }

        if (! filter_var($twitter, FILTER_VALIDATE_URL)) {
            return null;
        }

        $path = trim((string) parse_url($twitter, PHP_URL_PATH), '/');
        if ($path === '' || ! preg_match('/^@?([A-Za-z0-9_]{1,15})$/', $path, $matches)) {
            return null;
        }

        return '@'.ltrim($matches[1], '@');
    }

    protected function resolveSameAs(): array
    {
        if (! $this->siteSetting?->socialLinks) {
            return [];
        }

        return $this->siteSetting->socialLinks
            ->where('is_active', true)
            ->pluck('url')
            ->filter(fn (?string $url) => filled($url) && $url !== '#' && filter_var($url, FILTER_VALIDATE_URL))
            ->values()
            ->all();
    }

    protected function generateListSchema(string $type, string $title, string $desc, string $url, string $image): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $title,
            'description' => $desc,
            'url' => $url,
            'image' => $image,
        ];
    }

    protected function generateMedicalClinicSchema(string $desc, string $url, string $image): array
    {
        $homeUrl = $this->localizedPath('');
        $brand = $this->brandName();
        $logo = $this->defaultImage();
        $sameAs = $this->resolveSameAs();
        $phones = array_column($this->siteSetting?->ctaPhoneEntries() ?? [], 'tel');
        $email = $this->siteSetting?->support_email;

        $branches = Branch::query()->isActive()->orderBy('order')->get();
        $addresses = [];
        foreach ($branches as $branch) {
            $address = trim((string) $branch->getTranslation('address', $this->lang));
            if ($address !== '') {
                $addresses[] = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $address,
                ];
            }
        }

        $clinic = [
            '@type' => 'MedicalClinic',
            '@id' => $homeUrl.'#medicalclinic',
            'name' => $brand,
            'url' => $homeUrl,
            'logo' => ['@type' => 'ImageObject', 'url' => $logo],
            'image' => $image ?: $logo,
            'description' => $desc,
            'medicalSpecialty' => 'https://schema.org/Dentistry',
        ];

        if ($phones !== []) {
            $clinic['telephone'] = count($phones) === 1 ? $phones[0] : $phones;
        }
        if (filled($email)) {
            $clinic['email'] = $email;
        }
        if ($sameAs !== []) {
            $clinic['sameAs'] = $sameAs;
        }
        if (count($addresses) === 1) {
            $clinic['address'] = $addresses[0];
        } elseif (count($addresses) > 1) {
            $clinic['address'] = $addresses;
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $homeUrl.'#website',
                    'name' => $brand,
                    'url' => $homeUrl,
                    'publisher' => ['@id' => $homeUrl.'#medicalclinic'],
                ],
                [
                    '@type' => 'Physician',
                    '@id' => $homeUrl.'#physician',
                    'name' => $brand,
                    'url' => $homeUrl,
                    'image' => $image ?: $logo,
                    'description' => $desc,
                    'medicalSpecialty' => 'https://schema.org/Dentistry',
                ],
                $clinic,
                [
                    '@type' => 'WebPage',
                    'name' => $this->buildFullTitle(''),
                    'url' => $url,
                    'description' => $desc,
                    'isPartOf' => ['@id' => $homeUrl.'#website'],
                ],
            ],
        ];
    }
}
