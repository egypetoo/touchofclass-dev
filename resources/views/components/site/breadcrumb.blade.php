<section class="page-banner" style="background: url('{{ $backgroundImage }}') no-repeat center center / cover;">
    <div class="container">
        @if ($eyebrow)
            <span class="page-banner-eyebrow">{{ $eyebrow }}</span>
        @endif
        <h1 class="page-title">{!! $headingHtml !!}</h1>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                @if ($parentTitle && $parentUrl)
                    <li class="breadcrumb-item"><a href="{{ $parentUrl }}">{{ $parentTitle }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $crumbTitle ?: str_replace("\n", ' ', $pageTitle) }}</li>
            </ol>
        </nav>
    </div>
</section>
