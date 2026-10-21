# Site - General Blade Structure

A generic folder structure for e-commerce templates. Works for any shop/store name.

## Directory Structure

```
resources/views/
├── layouts/
│   └── site.blade.php              # Main layout (head, scripts, body wrapper)
│
├── partials/site/
│   ├── head.blade.php              # Meta, fonts, CSS links
│   ├── scripts.blade.php           # JS imports (end of body)
│   ├── header.blade.php
│   ├── header-top-bar.blade.php
│   ├── header-nav.blade.php
│   ├── header-mobile-nav.blade.php
│   ├── footer.blade.php
│   ├── floating-icons.blade.php
│   ├── svg-icons.blade.php
│   └── modals/
│       ├── search.blade.php
│       ├── quick-view.blade.php
│       ├── filter-sidebar.blade.php
│       └── new-year-popup.blade.php
│
├── components/site/
│   ├── product-card.blade.php
│   ├── icon-box.blade.php
│   ├── category-card.blade.php
│   ├── blog-card.blade.php
│   └── service-card.blade.php
│
└── site/
    ├── home.blade.php
    ├── catalog.blade.php
    ├── product-details.blade.php
    └── sections/
        ├── banner.blade.php
        ├── categories.blade.php
        ├── best-sellers.blade.php
        ├── featured.blade.php
        ├── aspire-banner.blade.php
        ├── product-tabs.blade.php
        ├── style-section.blade.php
        ├── showroom.blade.php
        ├── services.blade.php
        ├── icon-boxes.blade.php
        └── blog.blade.php
```

## Head & Scripts (CSS / JS)

Use `partials/site/head.blade.php` for meta, fonts, and CSS. Use `partials/site/scripts.blade.php` for JS imports.

### Layout (`layouts/site.blade.php`)

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    @include('partials.site.head')

    {{-- Page-specific CSS --}}
    @stack('styles')
</head>
<body>
    @yield('content')

    @include('partials.site.scripts')

    {{-- Page-specific JS --}}
    @stack('scripts')
</body>
</html>
```

### Head partial (`partials/site/head.blade.php`)

```blade
{{-- Favicon --}}
<link rel="shortcut icon" type="image/x-icon" href="{{ Path::imagesPath('others/logo.png') }}" />

{{-- Vendor CSS --}}
<link rel="stylesheet" href="{{ Path::vendorPath('fontawesome/css/all.min.css') }}" />
<link rel="stylesheet" href="{{ Path::vendorPath('animate/animate.min.css') }}" />
<link rel="stylesheet" href="{{ Path::vendorPath('slick/slick.css') }}" />
<link rel="stylesheet" href="{{ Path::vendorPath('lightgallery/css/lightgallery-bundle.min.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

{{-- App CSS --}}
<link rel="stylesheet" href="{{ Path::cssPath('theme.css') }}" />
```

### Scripts partial (`partials/site/scripts.blade.php`)

```blade
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ Path::vendorPath('bootstrap/js/bootstrap.bundle.js') }}"></script>
<script src="{{ Path::vendorPath('slick/slick.min.js') }}"></script>
<script src="{{ Path::vendorPath('lightgallery/lightgallery.min.js') }}"></script>
<script src="{{ Path::vendorPath('isotope/isotope.pkgd.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
<script src="{{ Path::jsPath('theme.min.js') }}"></script>
```

### Page-specific CSS / JS

Use `@push` in child views to add extra styles or scripts:

```blade
{{-- In site/home.blade.php or any page --}}
@extends('layouts.site')

@push('styles')
    <link rel="stylesheet" href="{{ Path::vendorPath('custom/slider.css') }}" />
@endpush

@section('content')
    ...
@endsection

@push('scripts')
    <script src="{{ Path::jsPath('home.js') }}"></script>
    <script>
        // Or inline JS
        document.addEventListener('DOMContentLoaded', function() { ... });
    </script>
@endpush
```

### Asset paths (Path helper)

| Method | Example |
|--------|---------|
| `Path::cssPath('theme.css')` | `assets/css/theme.css` |
| `Path::jsPath('theme.min.js')` | `assets/js/theme.min.js` |
| `Path::vendorPath('slick/slick.css')` | `assets/vendors/slick/slick.css` |
| `Path::imagesPath('others/logo.png')` | `assets/images/others/logo.png` |

---

## Usage

```blade
@extends('layouts.site')

@section('content')
    @include('partials.site.sections.banner')
    @include('partials.site.sections.categories')
    @include('components.site.product-card', ['product' => $product])
@endsection
```

**Shop name** comes from config – use `{{ config('app.shop_name') }}` in templates. Set via `SHOP_NAME` in `.env`.
