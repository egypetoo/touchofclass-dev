@props(['product'])

@php
    $imageUrl = $product->mainImageUrl() ?: Path::imagesPath('products/product-card-1.jpg');
    $specLabels = $product->tags->take(2)->pluck('name');
@endphp

<a href="{{ route('products.show', $product) }}" {{ $attributes->merge(['class' => 'product-item-card']) }}>
    @if ($product->category)
        <span class="product-category-pill">{{ mb_strtoupper($product->category->name) }}</span>
    @endif
    <div class="product-card-img-wrap">
        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" />
    </div>
    <div class="product-info-panel">
        <h3 class="product-card-title">{{ $product->name }}</h3>
        @if ($product->short_description)
            <p class="product-card-desc">{{ $product->short_description }}</p>
        @endif
        <div class="product-card-footer-row">
            <div class="product-specs-group">
                @foreach ($specLabels as $specLabel)
                    @if (! $loop->first)
                        <span class="product-spec-divider"></span>
                    @endif
                    <div class="product-spec-item">
                        <svg class="product-spec-icon" viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            @if ($loop->first)
                                <path d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            @else
                                <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
                                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
                            @endif
                        </svg>
                        <span>{{ $specLabel }}</span>
                    </div>
                @endforeach
            </div>
            <span class="product-action-square-btn" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="7" y1="17" x2="17" y2="7"></line>
                    <polyline points="7 7 17 7 17 17"></polyline>
                </svg>
            </span>
        </div>
    </div>
</a>
