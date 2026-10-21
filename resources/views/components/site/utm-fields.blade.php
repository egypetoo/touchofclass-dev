@php
    use App\Services\Marketing\UtmAttribution;

    $utmService = app(UtmAttribution::class);
    $utm = $utmService->fromSession();

    foreach (UtmAttribution::KEYS as $utmKey) {
        if (blank($utm[$utmKey] ?? null)) {
            $fromQuery = $utmService->sanitize(request()->query($utmKey));
            if ($fromQuery !== null) {
                $utm[$utmKey] = \Illuminate\Support\Str::limit($fromQuery, 191, '');
            }
        }
    }
@endphp

@foreach (UtmAttribution::KEYS as $utmKey)
    @if (filled($utm[$utmKey] ?? null))
        <input type="hidden" name="{{ $utmKey }}" value="{{ $utm[$utmKey] }}">
    @endif
@endforeach

@if (filled($utm['landing_page'] ?? null))
    <input type="hidden" name="landing_page" value="{{ $utm['landing_page'] }}">
@endif

@if (filled($utm['referrer'] ?? null))
    <input type="hidden" name="referrer" value="{{ $utm['referrer'] }}">
@endif
