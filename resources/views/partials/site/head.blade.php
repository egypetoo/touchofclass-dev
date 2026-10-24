<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('components.seo.head')

<link rel="icon" type="image/webp" href="{{ $siteLogoUrl ?? asset('assets/images/logo/logo-clear.png') }}" />
<link rel="manifest" href="{{ route('manifest') }}">
<meta name="theme-color" content="{{ \App\Models\PwaSetting::active()->theme_color ?? '#062e22' }}">

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400..700&display=swap" />

<link rel="preload" as="image" href="{{ $siteLogoUrl ?? asset('assets/images/logo/logo-clear.png') }}" />
@if (request()->routeIs('home'))
    <link rel="preload" as="image" href="{{ $siteLogoUrl ?? asset('assets/images/hero/hero-1.webp') }}" fetchpriority="high" />
@endif

<link rel="stylesheet" href="{{ Path::cssPath('all.min.css') }}" />
<link rel="stylesheet" href="{{ Path::cssPath('bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ Path::cssPath('swiper-bundle.min.css') }}" />
<link rel="stylesheet" href="{{ Path::cssPath('lenis.css') }}" />
<link rel="stylesheet" href="{{ Path::cssPath('style.css') }}" />
<link rel="stylesheet" href="{{ Path::cssPath('media.css') }}" />

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () { });
        });
    }
</script>