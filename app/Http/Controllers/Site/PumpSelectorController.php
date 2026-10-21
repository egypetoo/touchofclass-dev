<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PumpSelectorRequest;
use App\Models\Product;
use App\Models\Section;
use App\Services\PumpDutySelector;
use Illuminate\View\View;

class PumpSelectorController extends Controller
{
    public function __invoke(PumpSelectorRequest $request, PumpDutySelector $selector): View
    {
        $flow = $request->filled('flow') ? (float) $request->input('flow') : null;
        $head = $request->filled('head') ? (float) $request->input('head') : null;
        $duty = $selector->duty(
            $flow,
            (string) $request->input('flow_unit', 'm3h'),
            $head,
            (string) $request->input('head_unit', 'm'),
        );

        $pumps = Product::query()
            ->with(['media', 'category'])
            ->active()
            ->whereHas('category', function ($query): void {
                $query->where('is_active', true)
                    ->where(function ($query): void {
                        $query->where('slug->en', 'deep-well-pumps')
                            ->orWhereHas('parent', fn ($parent) => $parent->where('slug->en', 'deep-well-pumps'));
                    });
            })
            ->orderBy('sort_order')
            ->get();

        $split = $duty === null
            ? ['matches' => collect(), 'unrated' => collect()]
            : $selector->split($pumps, $duty['flow']);

        $sections = Section::query()
            ->page(PageEnum::HOME)
            ->IsActive()
            ->whereIn('type', [SectionTypeEnum::PROCESS, SectionTypeEnum::PROCESS_STEP])
            ->orderBy('order')
            ->get()
            ->groupBy(fn (Section $section): string => (string) $section->type);

        return view('site.pump-selector', [
            'duty' => $duty,
            'matchedPumps' => $split['matches'],
            'unratedPumps' => $split['unrated'],
            'homeSelector' => $sections->get(SectionTypeEnum::PROCESS->value)?->first(),
            'homeSelectorFeatures' => $sections->get(SectionTypeEnum::PROCESS_STEP->value, collect()),
        ]);
    }
}
