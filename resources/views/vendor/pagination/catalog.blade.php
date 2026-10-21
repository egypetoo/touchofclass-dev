@if ($paginator->hasPages())
    <nav class="catalog-pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        @if ($paginator->onFirstPage())
            <span class="page-circle-btn is-disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-circle-btn" rel="prev"
                aria-label="@lang('pagination.previous')">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-circle-btn is-disabled" aria-disabled="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-circle-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-circle-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-circle-btn" rel="next"
                aria-label="@lang('pagination.next')">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
        @else
            <span class="page-circle-btn is-disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </span>
        @endif
    </nav>
@endif
