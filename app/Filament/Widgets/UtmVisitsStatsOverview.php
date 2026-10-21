<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\UtmVisit;
use App\Services\Marketing\UtmAttribution;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UtmVisitsStatsOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = null;

    public ?string $from = null;

    public ?string $until = null;

    protected function getHeading(): ?string
    {
        return __('admin_trans.utm_visits_summary');
    }

    protected function getStats(): array
    {
        $utm = app(UtmAttribution::class);

        $visits = $this->visitsQuery()->count();
        $conversions = $this->conversionsQuery()->count();
        $platforms = $this->visitsQuery()
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->distinct()
            ->pluck('utm_source')
            ->count();

        $top = $this->visitsQuery()
            ->select('utm_source', DB::raw('COUNT(*) as aggregate'))
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->groupBy('utm_source')
            ->orderByDesc('aggregate')
            ->first();

        $topLabel = $top
            ? ($utm->platformLabel($top->utm_source) ?? $top->utm_source)
            : '—';

        return [
            Stat::make(__('admin_trans.utm_stat_visits'), number_format($visits))
                ->description(__('admin_trans.utm_stat_visits_help'))
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->color('primary'),
            Stat::make(__('admin_trans.utm_stat_conversions'), number_format($conversions))
                ->description(__('admin_trans.utm_stat_conversions_help'))
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->color('success'),
            Stat::make(__('admin_trans.utm_stat_platforms'), number_format($platforms))
                ->description(__('admin_trans.utm_stat_platforms_help'))
                ->descriptionIcon(Heroicon::OutlinedGlobeAlt)
                ->color('warning'),
            Stat::make(__('admin_trans.utm_stat_top_source'), $topLabel)
                ->description(__('admin_trans.utm_stat_top_source_help'))
                ->descriptionIcon(Heroicon::OutlinedTrophy)
                ->color('info'),
        ];
    }

    /**
     * @return Builder<UtmVisit>
     */
    protected function visitsQuery(): Builder
    {
        return UtmVisit::query()
            ->when($this->from, fn (Builder $query) => $query->whereDate('created_at', '>=', $this->from))
            ->when($this->until, fn (Builder $query) => $query->whereDate('created_at', '<=', $this->until));
    }

    /**
     * @return Builder<ContactMessage>
     */
    protected function conversionsQuery(): Builder
    {
        return ContactMessage::query()
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->when($this->from, fn (Builder $query) => $query->whereDate('created_at', '>=', $this->from))
            ->when($this->until, fn (Builder $query) => $query->whereDate('created_at', '<=', $this->until));
    }
}
