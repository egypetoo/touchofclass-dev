@php
    $metatags = $metatags ?? [];
    $siteName = $siteSetting?->siteName() ?? config('app.name');
    $title = $metatags['title'] ?? $siteName;
    $description = $metatags['description'] ?? '';
@endphp
<meta charset="{{ $metatags['charset'] ?? 'utf-8' }}">
<meta name="viewport" content="{{ $metatags['viewport'] ?? 'width=device-width, initial-scale=1.0' }}">
<meta http-equiv="Content-Language" content="{{ $metatags['language'] ?? app()->getLocale() }}">
<meta name="robots" content="{{ $metatags['robots'] ?? 'index, follow' }}">
<meta name="description" content="{{ $description }}">
@if (!empty($metatags['keywords']))
    <meta name="keywords" content="{{ $metatags['keywords'] }}">
@endif
<meta name="author" content="{{ $metatags['author'] ?? $siteName }}">
<title>{{ $title }}</title>

@if (!empty($metatags['canonical']))
    <link rel="canonical" href="{{ $metatags['canonical'] }}">
@endif

@php
    $favicon = $siteSetting?->faviconUrl() ?? asset('assets/images/logo/logo.png');
@endphp
<link rel="icon" href="{{ $favicon }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{ $favicon }}">

<meta property="og:title" content="{{ $metatags['og_title'] ?? $title }}">
<meta property="og:description" content="{{ $metatags['og_description'] ?? $description }}">
<meta property="og:url" content="{{ $metatags['og_url'] ?? url()->current() }}">
<meta property="og:image" content="{{ $metatags['og_image'] ?? ($siteSetting?->logoUrl() ?? '') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:type" content="{{ $metatags['og_type'] ?? 'website' }}">
<meta property="og:site_name" content="{{ $metatags['og_site_name'] ?? $siteName }}">
<meta property="og:locale" content="{{ $metatags['og_locale'] ?? app()->getLocale() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metatags['twitter_title'] ?? $title }}">
<meta name="twitter:description" content="{{ $metatags['twitter_description'] ?? $description }}">
<meta name="twitter:image" content="{{ $metatags['twitter_image'] ?? ($siteSetting?->logoUrl() ?? '') }}">
<meta name="twitter:url" content="{{ $metatags['twitter_url'] ?? url()->current() }}">
@if (!empty($metatags['twitter_site']))
    <meta name="twitter:site" content="{{ $metatags['twitter_site'] }}">
@endif
@if (!empty($metatags['twitter_creator']))
    <meta name="twitter:creator" content="{{ $metatags['twitter_creator'] }}">
@endif

@php
    $locales = array_keys(config('laravellocalization.supportedLocales', ['ar' => [], 'en' => []]));
    foreach ($locales as $locale) {
        $href = \App\Support\LocaleUrl::forLocale($locale);
        echo '<link rel="alternate" hreflang="' . e($locale) . '" href="' . e($href) . '" />' . "\n";
    }
    $xDefaultLocale = in_array('ar', $locales, true) ? 'ar' : ($locales[0] ?? 'en');
    $xDefaultUrl = \App\Support\LocaleUrl::forLocale($xDefaultLocale);
    echo '<link rel="alternate" hreflang="x-default" href="' . e($xDefaultUrl) . '" />' . "\n";
@endphp

@if (!empty($metatags['google_search_console_verification']))
    <meta name="google-site-verification" content="{{ $metatags['google_search_console_verification'] }}">
@endif

@if (!empty($metatags['google_tag_manager_id']))
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $metatags['google_tag_manager_id'] }}');
    </script>
@endif

@if (!empty($metatags['google_analytics_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $metatags['google_analytics_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $metatags['google_analytics_id'] }}');
    </script>
@endif

@if (!empty($schema))
    <script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

@if (!empty($faq_schema))
    <script type="application/ld+json">
{!! json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
