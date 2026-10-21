<?php

namespace App\Http\Controllers;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Models\Blog;
use App\Models\Partner;
use App\Models\Section;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $sections = Section::query()
            ->with('media')
            ->page(PageEnum::HOME)
            ->IsActive()
            ->orderBy('order')
            ->get()
            ->groupBy(fn (Section $section): string => (string) $section->type);

        $heroes = $sections->get(SectionTypeEnum::HERO->value, collect());

        return view('site.home.index', [
            'heroSlides' => $this->heroSlides($heroes),
            'homeAbout' => $sections->get(SectionTypeEnum::ABOUTUS->value)?->first(),
            'homeServicesIntro' => $sections->get(SectionTypeEnum::SERVICES->value)?->first(),
            'homeServices' => Service::query()->with('media')->roots()->isActive()->orderBy('order')->get(),
            'homeProductsIntro' => $sections->get(SectionTypeEnum::FEATURED_PRODUCTS->value)?->first(),
            'homeSelector' => $sections->get(SectionTypeEnum::PROCESS->value)?->first(),
            'homeSelectorFeatures' => $sections->get(SectionTypeEnum::PROCESS_STEP->value, collect()),
            'homeBrandsIntro' => $sections->get(SectionTypeEnum::CLIENTS->value)?->first(),
            'homePartners' => Partner::query()->with('media')->isActive()->showInHome()->orderBy('order')->get(),
            'homeWhy' => $sections->get(SectionTypeEnum::WHY_CHOOSE_US->value)?->first(),
            'homeBenefits' => $sections->get(SectionTypeEnum::BENEFITS->value, collect()),
            'homeStats' => $sections->get(SectionTypeEnum::STATISTICS->value, collect()),
            'homeArticlesIntro' => $sections->get(SectionTypeEnum::BLOGS->value)?->first(),
            'homeArticles' => Blog::query()->with('media')->isActive()->orderByDesc('published_at')->limit(4)->get(),
            'homeContact' => $sections->get(SectionTypeEnum::CONTACT_US_CONTENT->value)?->first(),
        ]);
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
