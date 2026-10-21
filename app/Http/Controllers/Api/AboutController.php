<?php

namespace App\Http\Controllers\Api;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Models\Section;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = [
            'breadcrumb_section' => Section::query()
                ->page(PageEnum::ABOUT)
                ->type(SectionTypeEnum::ABOUT_BREADCRUMB)
                ->IsActive()
                ->orderBy('order')
                ->first(),
            'about_us_section' => Section::query()
                ->page(PageEnum::ABOUT)
                ->type(SectionTypeEnum::ABOUTUS)
                ->IsActive()
                ->orderBy('order')
                ->first(),
            'values_section' => Section::query()
                ->page(PageEnum::ABOUT)
                ->type(SectionTypeEnum::VALUES)
                ->IsActive()
                ->orderBy('order')
                ->get(),
            'statistics_section' => Section::query()
                ->page(PageEnum::ABOUT)
                ->type(SectionTypeEnum::STATISTICS)
                ->IsActive()
                ->orderBy('order')
                ->get(),
        ];

        return new AboutResource($data);
    }
}
