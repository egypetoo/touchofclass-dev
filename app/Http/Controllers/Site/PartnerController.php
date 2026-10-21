<?php

namespace App\Http\Controllers\Site;

use App\Enums\ClientSectorEnum;
use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Section;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function index(): View
    {
        $partners = Partner::query()
            ->with('media')
            ->isActive()
            ->orderBy('order')
            ->get();

        $partnersBySector = $partners->groupBy(fn (Partner $partner): string => $partner->sector?->value ?? '');

        $sectorGroups = collect(ClientSectorEnum::cases())
            ->map(fn (ClientSectorEnum $sector): array => [
                'sector' => $sector,
                'partners' => $partnersBySector->get($sector->value, collect()),
            ])
            ->filter(fn (array $group): bool => $group['partners']->isNotEmpty())
            ->values();

        $unassignedPartners = $partnersBySector->get('', collect());

        return view('site.partners.index', [
            'clients_breadcrumb' => Section::query()
                ->with('media')
                ->page(PageEnum::CLIENTS)
                ->type(SectionTypeEnum::BREADCRUMB)
                ->isActive()
                ->first(),
            'sectorGroups' => $sectorGroups,
            'unassignedPartners' => $unassignedPartners,
        ]);
    }
}
