<div class="clients-category" id="{{ $categoryId }}">
    <div class="clients-category-head" data-aos="fade-up">
        <span class="clients-category-index">{{ str_pad((string) $categoryIndex, 2, '0', STR_PAD_LEFT) }}</span>
        <div>
            <h2 class="clients-category-title font-serif-heading">{{ $categoryTitle }}</h2>
            <span class="clients-category-count">{{ trans_choice('site.clients_count', $categoryPartners->count(), ['count' => $categoryPartners->count()]) }}</span>
        </div>
    </div>
    <div class="row g-3" data-aos="fade-up" data-aos-delay="100">
        @foreach ($categoryPartners as $partner)
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <div class="client-logo-card" title="{{ $partner->name }}">
                    @if ($partner->imageUrl())
                        @if ($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->name }}" loading="lazy" />
                            </a>
                        @else
                            <img src="{{ $partner->imageUrl() }}" alt="{{ $partner->name }}" loading="lazy" />
                        @endif
                    @else
                        <span class="fw-semibold">{{ $partner->name }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
