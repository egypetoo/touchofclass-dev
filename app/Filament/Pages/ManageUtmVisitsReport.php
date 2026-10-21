<?php

namespace App\Filament\Pages;

use App\Models\ContactMessage;
use App\Models\UtmVisit;
use App\Services\Marketing\UtmAttribution;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ManageUtmVisitsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected string $view = 'filament.pages.manage-utm-visits-report';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var list<array{source: string, label: string, visits: int, conversions: int}>
     */
    public array $byPlatform = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.utm_visits_report');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Company';
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_trans.utm_visits_report');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('admin_trans.utm_visits_report_help');
    }

    public function mount(): void
    {
        $this->form->fill([
            'from' => now()->subDays(30)->toDateString(),
            'until' => now()->toDateString(),
        ]);

        $this->refreshByPlatform();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin_trans.utm_date_range'))
                    ->description(__('admin_trans.utm_date_range_help'))
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('admin_trans.utm_date_from'))
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->live()
                            ->afterStateUpdated(fn () => $this->onDateFiltersUpdated()),
                        DatePicker::make('until')
                            ->label(__('admin_trans.utm_date_until'))
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->live()
                            ->afterStateUpdated(fn () => $this->onDateFiltersUpdated()),
                    ])
                    ->columns(2)
                    ->compact(),
            ]);
    }

    public function getFromDate(): ?string
    {
        return filled($this->data['from'] ?? null) ? (string) $this->data['from'] : null;
    }

    public function getUntilDate(): ?string
    {
        return filled($this->data['until'] ?? null) ? (string) $this->data['until'] : null;
    }

    public function onDateFiltersUpdated(): void
    {
        $this->refreshByPlatform();
        $this->resetTable();
    }

    public function refreshByPlatform(): void
    {
        $utm = app(UtmAttribution::class);
        $from = $this->getFromDate();
        $until = $this->getUntilDate();

        $visitCounts = UtmVisit::query()
            ->select('utm_source', DB::raw('COUNT(*) as visits'))
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->when($from, fn (Builder $query) => $query->whereDate('created_at', '>=', $from))
            ->when($until, fn (Builder $query) => $query->whereDate('created_at', '<=', $until))
            ->groupBy('utm_source')
            ->pluck('visits', 'utm_source');

        $conversionCounts = ContactMessage::query()
            ->select('utm_source', DB::raw('COUNT(*) as conversions'))
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->when($from, fn (Builder $query) => $query->whereDate('created_at', '>=', $from))
            ->when($until, fn (Builder $query) => $query->whereDate('created_at', '<=', $until))
            ->groupBy('utm_source')
            ->pluck('conversions', 'utm_source');

        $sources = $visitCounts->keys()->merge($conversionCounts->keys())->unique()->sort()->values();

        $this->byPlatform = $sources->map(function (string $source) use ($visitCounts, $conversionCounts, $utm): array {
            return [
                'source' => $source,
                'label' => $utm->platformLabel($source) ?? $source,
                'visits' => (int) ($visitCounts[$source] ?? 0),
                'conversions' => (int) ($conversionCounts[$source] ?? 0),
            ];
        })->sortByDesc('visits')->values()->all();
    }

    public function table(Table $table): Table
    {
        $utm = app(UtmAttribution::class);

        return $table
            ->query(fn (): Builder => UtmVisit::query()
                ->latest()
                ->when($this->getFromDate(), fn (Builder $query) => $query->whereDate('created_at', '>=', $this->getFromDate()))
                ->when($this->getUntilDate(), fn (Builder $query) => $query->whereDate('created_at', '<=', $this->getUntilDate())))
            ->columns([
                TextColumn::make('utm_source')
                    ->label(__('admin_trans.utm_platform'))
                    ->formatStateUsing(fn (?string $state): string => $utm->platformLabel($state) ?? ($state ?: '—'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('utm_medium')
                    ->label(__('admin_trans.utm_medium'))
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('utm_campaign')
                    ->label(__('admin_trans.utm_campaign'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('path')
                    ->label(__('admin_trans.utm_path'))
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('referrer')
                    ->label(__('admin_trans.utm_referrer'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('utm_source')
                    ->label(__('admin_trans.utm_platform'))
                    ->options(fn (): array => UtmVisit::query()
                        ->whereNotNull('utm_source')
                        ->distinct()
                        ->orderBy('utm_source')
                        ->pluck('utm_source', 'utm_source')
                        ->mapWithKeys(fn (string $source): array => [
                            $source => $utm->platformLabel($source) ?? $source,
                        ])
                        ->all()),
            ])
            ->defaultPaginationPageOption(15);
    }
}
