<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-5 mb-6">
        @foreach ($summary as $metric => $ratings)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                <div class="text-sm font-semibold uppercase mb-2">{{ strtoupper($metric) }}</div>
                <div class="text-xs text-success-600">Good: {{ $ratings['good'] ?? 0 }}</div>
                <div class="text-xs text-warning-600">Needs improvement: {{ $ratings['needs-improvement'] ?? 0 }}</div>
                <div class="text-xs text-danger-600">Poor: {{ $ratings['poor'] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    {{ $this->table }}
</x-filament-panels::page>
