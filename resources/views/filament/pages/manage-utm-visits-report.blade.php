<x-filament-panels::page>
    <div class="fi-section-content-ctn mb-6">
        {{ $this->form }}
    </div>

    <div class="mb-6">
        @livewire(\App\Filament\Widgets\UtmVisitsStatsOverview::class, [
            'from' => $this->getFromDate(),
            'until' => $this->getUntilDate(),
        ], key('utm-stats-' . ($this->getFromDate() ?? 'all') . '-' . ($this->getUntilDate() ?? 'all')))
    </div>

    @if ($byPlatform !== [])
        <x-filament::section
            :heading="__('admin_trans.utm_by_platform')"
            :description="__('admin_trans.utm_by_platform_help')"
            class="mb-6"
        >
            <div class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach ($byPlatform as $row)
                    <div class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $row['label'] }}
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300">
                            <span>
                                <span class="font-medium text-gray-950 dark:text-white">{{ number_format($row['visits']) }}</span>
                                {{ __('admin_trans.utm_stat_visits') }}
                            </span>
                            <span>
                                <span class="font-medium text-gray-950 dark:text-white">{{ number_format($row['conversions']) }}</span>
                                {{ __('admin_trans.utm_stat_conversions') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
