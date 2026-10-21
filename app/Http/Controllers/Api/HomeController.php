<?php

namespace App\Http\Controllers\Api;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\HomeResource;
use App\Models\Blog;
use App\Models\Section;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = [
            'hero' => Section::query()
                ->page(PageEnum::HOME)
                ->type(SectionTypeEnum::HERO)
                ->IsActive()
                ->orderBy('order')
                ->get(),

            'about_us' => Section::query()
                ->page(PageEnum::HOME)
                ->type(SectionTypeEnum::ABOUTUS)
                ->IsActive()
                ->orderBy('order')
                ->first(),
            'statistics_section' => Section::query()
                ->page(PageEnum::ABOUT)
                ->type(SectionTypeEnum::STATISTICS)
                ->IsActive()
                ->orderBy('order')
                ->take(3)
                ->get(),
        
            'video_section' => Section::query()
                ->page(PageEnum::HOME)
                ->type(SectionTypeEnum::VIDEO)
                ->IsActive()
                ->orderBy('order')
                ->first(),

            'why_choose_us_section' => Section::query()
                ->page(PageEnum::HOME)
                ->type(SectionTypeEnum::WHY_CHOOSE_US_BREADCRUMB)
                ->IsActive()
                ->orderBy('order')
                ->first(),
            'benefits' => Section::query()
                ->page(PageEnum::HOME)
                ->type(SectionTypeEnum::BENEFITS)
                ->IsActive()
                ->orderBy('order')
                ->get(),
            'blogs_section' => Section::query()
                ->page(PageEnum::HOME)
                ->type(SectionTypeEnum::BLOGS)
                ->IsActive()
                ->orderBy('order')
                ->first(),
            'blogs' => Blog::query()
                ->IsActive()
                ->orderBy('order')
                ->take(3)
                ->get(),
        ];

        return HomeResource::make($data);
    }
}
