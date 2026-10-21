<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\View\View;

class PageController extends Controller
{
    public function privacyPolicy(): View
    {
        $privacyPolicy = Section::query()
            ->page(PageEnum::PRIVACY_POLICY)
            ->type(SectionTypeEnum::CONTENT)
            ->isActive()
            ->orderBy('order')
            ->first();

        return view('site.pages.privacy-policy', compact('privacyPolicy'));
    }

    public function termsAndConditions(): View
    {
        $termsAndConditions = Section::query()
            ->page(PageEnum::TERMS_AND_CONDITIONS)
            ->type(SectionTypeEnum::CONTENT)
            ->isActive()
            ->orderBy('order')
            ->first();

        return view('site.pages.terms-and-conditions', compact('termsAndConditions'));
    }
}
