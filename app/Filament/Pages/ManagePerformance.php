<?php

namespace App\Filament\Pages;

use App\Models\PerformanceMetric;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class ManagePerformance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 13;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected string $view = 'filament.pages.manage-performance';

    /**
     * @var array<string, array<string, int>>
     */
    public array $summary = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.performance_monitoring');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.performance_monitoring');
    }

    public function mount(): void
    {
        $metrics = ['CLS', 'FID', 'LCP', 'FCP', 'TTFB', 'INP'];
        $ratings = ['good', 'needs-improvement', 'poor'];

        foreach ($metrics as $metric) {
            $this->summary[strtolower($metric)] = array_fill_keys($ratings, 0);
        }

        $rows = PerformanceMetric::query()
            ->select('metric', 'rating', DB::raw('COUNT(*) as count'))
            ->groupBy('metric', 'rating')
            ->get();

        foreach ($rows as $row) {
            $key = strtolower($row->metric);
            if (isset($this->summary[$key][$row->rating])) {
                $this->summary[$key][$row->rating] = (int) $row->count;
            }
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PerformanceMetric::query()->latest())
            ->columns([
                TextColumn::make('page')->label(__('admin_trans.page'))->limit(40),
                TextColumn::make('metric')->label(__('admin_trans.metric'))->badge(),
                TextColumn::make('value')->label(__('admin_trans.value')),
                TextColumn::make('rating')->label(__('admin_trans.rating'))->badge(),
                TextColumn::make('created_at')->label(__('admin_trans.created_at'))->dateTime(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
