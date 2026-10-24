<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('components.seo.head')

<link rel="icon" type="image/webp" href="{{ $siteLogoUrl ?? asset('assets/images/logo/logo-clear.png') }}" />
<link rel="manifest" href="{{ route('manifest') }}">
<meta name="theme-color" content="{{ \App\Models\PwaSetting::active()->theme_color ?? '#062e22' }}">

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'" />
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" /></noscript>

@if (request()->routeIs('home') && isset($heroSlides[0]['imageMobile']))
    <link rel="preload" as="image" href="{{ $heroSlides[0]['imageMobile'] }}" fetchpriority="high" />
@endif

<link rel="stylesheet" href="{{ Path::cssPath('all.min.css') }}" media="print" onload="this.media='all'" />
<noscript><link rel="stylesheet" href="{{ Path::cssPath('all.min.css') }}" /></noscript>
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