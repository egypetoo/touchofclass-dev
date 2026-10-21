<section class="solutions-section py-5">
    <div class="container py-4">
        <div class="row g-4 gy-4">
            @forelse ($serviceCards as $card)
                <div class="col-md-6 col-lg-4 d-flex" data-aos="fade-up" data-aos-delay="{{ 50 * (($loop->index % 3) + 1) }}">
                    <a href="{{ $card->url }}" class="solution-grid-card w-100">
                        <div class="solution-grid-header">
                            @if ($card->label)
                                <span class="solution-grid-pill">{{ $card->label }}</span>
                            @endif
                            <span class="solution-grid-number">{{ $card->number }}</span>
                        </div>
                        <div class="solution-grid-img-wrap">
                            <img src="{{ $card->imageUrl }}" alt="{{ $card->title }}" loading="lazy" />
                        </div>
                        <div class="solution-grid-body">
                            <h3 class="solution-grid-title">{{ $card->title }}</h3>
                            @if ($card->excerpt)
                                <p class="solution-grid-desc">{{ $card->excerpt }}</p>
                            @endif
                        </div>
                        <div class="solution-grid-footer">
                            <span class="solution-grid-action-btn" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                    <polyline points="7 7 17 7 17 17"></polyline>
                                </svg>
                            </span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">{{ __('site.no_items_yet') }}</div>
            @endforelse
        </div>
    </div>
</section>
