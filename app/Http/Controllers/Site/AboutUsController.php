<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $sections = Section::query()
            ->with('media')
            ->page(PageEnum::ABOUT)
            ->IsActive()
            ->orderBy('order')
            ->get()
            ->groupBy(fn (Section $section): string => (string) $section->type);

        return view('site.about-us.index', [
            'breadcrumb' => $sections->get(SectionTypeEnum::ABOUT_BREADCRUMB->value)?->first(),
            'about_us' => $sections->get(SectionTypeEnum::ABOUTUS->value)?->first(),
            'statistics' => $sections->get(SectionTypeEnum::STATISTICS->value, collect())->values(),
            'vision' => $sections->get(SectionTypeEnum::VISION->value)?->first(),
            'mission' => $sections->get(SectionTypeEnum::MISSION->value)?->first(),
            'founders' => Team::query()
                ->isActive()
                ->orderBy('order')
                ->get()
                ->map(fn (Team $member): array => [
                    'name' => (string) $member->name,
                    'title' => (string) $member->title,
                    'image' => $member->imageUrl(),
                ])
                ->all(),
        ]);
    }
}
