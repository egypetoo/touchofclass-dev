@props([
    'enabled' => false,
    'siteKey' => null,
])

@if ($enabled && filled($siteKey))
    <div class="col-12">
        <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="light"></div>
    </div>
    @once
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce
@endif
