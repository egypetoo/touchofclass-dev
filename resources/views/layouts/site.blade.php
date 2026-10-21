<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    @include('partials.site.head')
    @stack('styles')
</head>

<body>
    <div id="loader" class="loading">
        @include('partials.site._loader')
    </div>

    <header class="header fixed-top top-0 start-0 end-0 header-main px-md-5" id="header">
        @include('partials.site._navbar')
    </header>

    @if (session('success'))
        <div class="container" style="padding-top: 7.5rem;">
            <div class="alert alert-success mb-0" role="status">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="container" style="padding-top: 7.5rem;">
            <div class="alert alert-danger mb-0" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @yield('content')

    <footer id="footer" class="footer km-footer">
        @include('partials.site.footer')
    </footer>

    <div class="back-to-top" id="back-top">
        <button type="button" class="d-flex justify-content-center align-items-center" aria-label="{{ __('site.back_to_top') }}">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>

    @include('partials.site.scripts')
</body>

</html>
