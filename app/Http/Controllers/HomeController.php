<?php

namespace App\Http\Controllers;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Models\Blog;
use App\Models\Branch;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Models\Service;
use App\Models\SiteSocialLink;
use App\Services\SiteLayoutDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function __invoke(SiteLayoutDataService $layoutData): View
    {
        $sections = Section::query()
            ->with('media')
            ->page(PageEnum::HOME)
            ->IsActive()
            ->orderBy('order')
            ->get()
            ->groupBy(fn (Section $section): string => (string) $section->type);

        $heroes = $sections->get(SectionTypeEnum::HERO->value, collect());
        $homeProjectCategories = $this->homeProjectCategories();
        $homeServices = Service::query()->with('media')->roots()->isActive()->orderBy('order')->get();

        return view('site.home.index', [
            'heroSlides' => $this->heroSlides($heroes),
            'homeAbout' => $this->homeAbout($sections->get(SectionTypeEnum::ABOUTUS->value)?->first()),
            'homeServicesIntro' => $sections->get(SectionTypeEnum::SERVICES->value)?->first(),
            'homeServices' => $homeServices,
            'homeProjectsIntro' => $sections->get(SectionTypeEnum::FEATURED_PRODUCTS->value)?->first(),
            'homeProjectCategories' => $homeProjectCategories,
            'homeProjectTabColumn' => match ($homeProjectCategories->count()) {
                1 => 'col-12',
                2 => 'col-6',
                3 => 'col-6 col-lg-4',
                default => 'col-6 col-lg-3',
            },
            'homeSelector' => $sections->get(SectionTypeEnum::PROCESS->value)?->first(),
            'homeSelectorFeatures' => $sections->get(SectionTypeEnum::PROCESS_STEP->value, collect()),
            'homeBrandsIntro' => $sections->get(SectionTypeEnum::CLIENTS->value)?->first(),
            'homePartners' => Partner::query()->with('media')->isActive()->showInHome()->orderBy('order')->get(),
            'homeWhy' => $this->homeWhy(
                $sections->get(SectionTypeEnum::WHY_CHOOSE_US->value)?->first(),
                $sections->get(SectionTypeEnum::BENEFITS->value, collect()),
                $sections->get(SectionTypeEnum::STATISTICS->value, collect()),
            ),
            'homeBlogsIntro' => $sections->get(SectionTypeEnum::BLOGS->value)?->first(),
            ...$this->homeArticleCards(),
            'homeContact' => $this->homeContact(
                $sections->get(SectionTypeEnum::CONTACT_US_CONTENT->value)?->first(),
                $homeServices,
                $layoutData,
            ),
        ]);
    }

    /**
     * @return Collection<int, array{slug: string, name: string, description: string, icon: string, projects: list<array{name: string, url: string, value: int, images: list<string>}>}>
     */
    private function homeProjectCategories(): Collection
    {
        return ProjectCategory::query()
            ->isActive()
            ->orderBy('order')
            ->with(['projects' => fn ($query) => $query->isActive()->showInHome()->orderBy('order')->with('media')])
            ->get()
            ->map(function (ProjectCategory $category): array {
                return [
                    'slug' => (string) $category->getTranslation('slug', 'en'),
                    'name' => (string) $category->name,
                    'description' => trim(strip_tags((string) $category->short_text)),
                    'icon' => filled($category->icon) ? (string) $category->icon : 'fa-solid fa-box',
                    'projects' => $category->projects->map(function (Project $project): array {
                        $images = $project->galleryLightboxUrls()->filter()->values()->all();

                        if ($images === []) {
                            $images = [asset('assets/images/studio/zayed-villa.jpg')];
                        }

                        return [
                            'name' => (string) $project->title,
                            'url' => route('projects.show', $project),
                            'value' => (int) $project->order,
                            'images' => array_slice($images, 0, 4),
                        ];
                    })->all(),
                ];
            })
            ->values();
    }

    /**
     * @param  Collection<int, Section>  $benefits
     * @param  Collection<int, Section>  $stats
     * @return array{eyebrow: string, lines: list<string>, text: string, benefits: list<array{icon: string, title: string, text: string}>, stats: list<array{number: int, suffix: string, text: string}>}
     */
    private function homeWhy(?Section $section, Collection $benefits, Collection $stats): array
    {
        $lines = $section?->titleLines() ?? [];
        $fallbackLines = __('site.home_why_lines');
        $icons = [
            'fa-solid fa-gem',
            'fa-solid fa-user',
            'fa-solid fa-lightbulb',
            'fa-solid fa-handshake',
        ];

        $benefitRows = $benefits->isNotEmpty()
            ? $benefits->values()->map(fn (Section $benefit, int $index): array => [
                'icon' => $icons[$index] ?? 'fa-solid fa-circle-check',
                'title' => trim(strip_tags((string) $benefit->title)),
                'text' => trim(strip_tags((string) $benefit->text)),
            ])->all()
            : collect(__('site.home_benefits'))->values()->map(fn (array $benefit, int $index): array => [
                'icon' => $icons[$index] ?? 'fa-solid fa-circle-check',
                'title' => (string) ($benefit['title'] ?? ''),
                'text' => (string) ($benefit['text'] ?? ''),
            ])->all();

        $statRows = $stats->isNotEmpty()
            ? $stats->values()->map(function (Section $stat): array {
                $parts = $stat->statisticParts();

                return [
                    'number' => $parts['number'],
                    'suffix' => $parts['suffix'],
                    'text' => trim(strip_tags((string) $stat->text)),
                ];
            })->all()
            : collect(__('site.home_stats'))->values()->map(function (array $stat): array {
                $parts = $this->statisticParts((string) ($stat['title'] ?? ''));

                return [
                    'number' => $parts['number'],
                    'suffix' => $parts['suffix'],
                    'text' => (string) ($stat['text'] ?? ''),
                ];
            })->all();

        return [
            'eyebrow' => (string) ($section?->sub_title ?: __('site.home_why_eyebrow')),
            'lines' => $lines !== [] ? $lines : (is_array($fallbackLines) ? $fallbackLines : [(string) $fallbackLines]),
            'text' => trim(strip_tags((string) ($section?->text ?: __('site.home_why_text')))),
            'benefits' => $benefitRows,
            'stats' => $statRows,
        ];
    }

    /**
     * @return array{number: int, suffix: string}
     */
    private function statisticParts(string $title): array
    {
        preg_match('/(\d+)(.*)/u', $title, $matches);

        return [
            'number' => (int) ($matches[1] ?? 0),
            'suffix' => trim($matches[2] ?? ''),
        ];
    }

    /**
     * @return array{homeFeaturedArticle: array{url: string, image: string, alt: string, datetime: string, date: string, title: string, excerpt: string}|null, homeArticles: list<array{url: string, image: string, alt: string, datetime: string, date: string, title: string, excerpt: string}>}
     */
    private function homeArticleCards(): array
    {
        $fallbacks = [
            'studio/photo-1.jpg',
            'studio/photo-2.jpg',
            'studio/photo-3.jpg',
            'studio/photo-4.jpg',
        ];

        $articles = Blog::query()
            ->with('media')
            ->isActive()
            ->orderByDesc('published_at')
            ->limit(4)
            ->get()
            ->values()
            ->map(function (Blog $blog, int $index) use ($fallbacks): array {
                return [
                    'url' => route('news.show', $blog),
                    'image' => Path::mediaOrDesign(
                        $blog->getFirstMediaUrl('blogs') ?: null,
                        $fallbacks[$index] ?? 'studio/photo-2.jpg',
                        allowOversized: true,
                    ),
                    'alt' => (string) $blog->title,
                    'datetime' => $blog->published_at?->toDateString() ?? '',
                    'date' => $blog->published_at?->translatedFormat('j F Y') ?? '',
                    'title' => (string) $blog->title,
                    'excerpt' => trim(strip_tags((string) $blog->excerpt)),
                ];
            })
            ->all();

        return [
            'homeFeaturedArticle' => $articles[0] ?? null,
            'homeArticles' => array_slice($articles, 1),
        ];
    }

    /**
     * @param  Collection<int, Service>  $services
     * @return array{
     *     eyebrow: string,
     *     line: string,
     *     second: string,
     *     text: string,
     *     cards: list<array{icon: string, title: string, href: ?string, text: string, lines: list<string>, ltr: bool}>,
     *     socials: list<array{url: string, label: string, icon: string, external: bool}>,
     *     form: array{lead: string, rest: string, description: string, action: string, requests: list<array{id: int, title: string}>},
     *     mapHtml: string,
     *     mapUrl: string
     * }
     */
    private function homeContact(?Section $section, Collection $services, SiteLayoutDataService $layoutData): array
    {
        $setting = $layoutData->siteSetting();
        $branch = $layoutData->mainBranch();
        $lines = $section?->titleLines() ?? [];
        $fallbackLines = __('site.home_contact_lines');

        if ($lines === []) {
            $lines = is_array($fallbackLines) ? array_values($fallbackLines) : [(string) $fallbackLines];
        }

        $phone = $setting?->ctaPhoneEntries()[0] ?? $this->branchPhone($branch);
        $email = $setting?->support_email ?: $branch?->email;
        $address = trim(strip_tags((string) ($branch?->address ?? '')));
        $hourLines = $this->hourLines((string) ($branch?->working_hours ?? ''));

        $cards = array_values(array_filter([
            $phone ? [
                'icon' => 'fa-solid fa-phone-volume',
                'title' => (string) __('site.home_contact_eyebrow'),
                'href' => str_starts_with($phone['tel'], 'tel:') ? $phone['tel'] : 'tel:'.$phone['tel'],
                'text' => $phone['display'],
                'lines' => [],
                'ltr' => true,
            ] : null,
            filled($email) ? [
                'icon' => 'fa-regular fa-envelope',
                'title' => (string) __('site.email'),
                'href' => 'mailto:'.$email,
                'text' => (string) $email,
                'lines' => [],
                'ltr' => false,
            ] : null,
            $address !== '' ? [
                'icon' => 'fa-solid fa-location-dot',
                'title' => (string) __('site.branch_address'),
                'href' => null,
                'text' => $address,
                'lines' => [],
                'ltr' => false,
            ] : null,
            $hourLines !== [] ? [
                'icon' => 'fa-regular fa-clock',
                'title' => (string) __('site.working_hours'),
                'href' => null,
                'text' => '',
                'lines' => $hourLines,
                'ltr' => false,
            ] : null,
        ]));

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

        $whatsappUrl = $setting?->ctaWhatsappEntries()[0]['url'] ?? null;

        if (filled($whatsappUrl)) {
            $socials[] = [
                'url' => $whatsappUrl,
                'label' => (string) __('site.home_whatsapp'),
                'icon' => 'fa-brands fa-whatsapp',
                'external' => true,
            ];
        }

        return [
            'eyebrow' => (string) ($section?->sub_title ?: __('site.home_contact_eyebrow')),
            'line' => (string) ($lines[0] ?? ''),
            'second' => filled($section?->second_title) ? (string) $section->second_title : (string) ($lines[1] ?? ''),
            'text' => trim(strip_tags((string) ($section?->text ?: __('site.home_contact_text')))),
            'cards' => $cards,
            'socials' => $socials,
            'form' => [
                'lead' => (string) __('site.home_contact_form_lead'),
                'rest' => (string) __('site.home_contact_form_rest'),
                'description' => (string) __('site.home_contact_form_desc'),
                'action' => route('contact.store'),
                'requests' => $services->map(fn (Service $service): array => [
                    'id' => $service->id,
                    'title' => (string) $service->title,
                ])->all(),
            ],
            'mapHtml' => $branch?->mapEmbedHtml() ?? '',
            'mapUrl' => (string) ($branch?->map_url ?: ''),
        ];
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
    private function hourLines(string $hours): array
    {
        $hours = trim(strip_tags($hours));

        if ($hours === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/u', $hours) ?: [];
        $lines = array_values(array_filter(array_map(trim(...), $lines), fn (string $line): bool => $line !== ''));

        if (count($lines) === 1 && preg_match('/^(.+?)[،,]\s*(.+)$/u', $lines[0], $matches) === 1) {
            return [trim($matches[1]), trim($matches[2])];
        }

        return $lines;
    }

    /**
     * @return array{eyebrow: string, lines: list<string>, text: string, button: string, url: string, images: list<string>, alt: string}
     */
    private function homeAbout(?Section $section): array
    {
        $lines = $section?->titleLines() ?? [];
        $fallbackLines = __('site.home_about_lines');

        return [
            'eyebrow' => (string) ($section?->sub_title ?: __('site.home_about_eyebrow')),
            'lines' => $lines !== [] ? $lines : (is_array($fallbackLines) ? $fallbackLines : [(string) $fallbackLines]),
            'text' => trim(strip_tags((string) ($section?->text ?: __('site.home_about_text')))),
            'button' => (string) ($section?->button_text ?: __('site.home_about_button')),
            'url' => $section?->button_link_url ?: route('about-us'),
            'images' => [
                asset('assets/images/studio/interior-design.jpg'),
                $section?->imageUrl('studio/about-main.jpg') ?? asset('assets/images/studio/about-main.jpg'),
            ],
            'alt' => (string) ($section?->alt_image ?: $section?->title ?: __('site.home_brand')),
        ];
    }

    /**
     * @param  Collection<int, Section>  $heroes
     * @return list<array{eyebrow: string, lines: list<string>, text: string, button: string, url: string, image: string, alt: string}>
     */
    private function heroSlides(Collection $heroes): array
    {
        if ($heroes->isEmpty()) {
            return [
                [
                    'eyebrow' => __('site.home_brand'),
                    'lines' => __('site.home_hero_1_lines'),
                    'text' => __('site.home_hero_1_text'),
                    'button' => __('site.home_hero_1_button'),
                    'url' => route('products.index'),
                    'image' => asset('assets/images/hero/hero-1.webp'),
                    'alt' => __('site.home_hero_1_alt'),
                ],
                [
                    'eyebrow' => __('site.home_brand'),
                    'lines' => __('site.home_hero_2_lines'),
                    'text' => __('site.home_hero_2_text'),
                    'button' => __('site.home_hero_2_button'),
                    'url' => route('products.index'),
                    'image' => asset('assets/images/hero/hero-2.webp'),
                    'alt' => __('site.home_hero_2_alt'),
                ],
                [
                    'eyebrow' => __('site.home_brand'),
                    'lines' => __('site.home_hero_3_lines'),
                    'text' => __('site.home_hero_3_text'),
                    'button' => __('site.home_hero_3_button'),
                    'url' => route('services.index'),
                    'image' => asset('assets/images/hero/hero-3.webp'),
                    'alt' => __('site.home_hero_3_alt'),
                ],
            ];
        }

        return $heroes->map(function (Section $section): array {
            $lines = $section->titleLines();

            return [
                'eyebrow' => (string) ($section->sub_title ?: __('site.home_brand')),
                'lines' => $lines !== [] ? $lines : [strip_tags((string) $section->title)],
                'text' => trim(strip_tags((string) $section->text)),
                'button' => (string) ($section->button_text ?: __('site.our_products')),
                'url' => $section->button_link_url ?: route('products.index'),
                'image' => $section->imageUrl('hero/hero-1.webp'),
                'alt' => (string) ($section->alt_image ?: $section->title),
            ];
        })->all();
    }
}
