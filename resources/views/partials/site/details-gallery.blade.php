@if (count($galleryImages) > 0)
    <div class="details-slider-wrapper is-photo" data-aos="fade-up">
        <div class="swiper detailsSwiper">
            <div class="swiper-wrapper">
                @foreach ($galleryImages as $galleryImage)
                    <div class="swiper-slide">
                        <a href="{{ $galleryImage }}" data-fancybox="{{ $galleryName }}" data-caption="{{ $galleryCaption }}"
                            class="details-slide-item is-photo">
                            <img src="{{ $galleryImage }}" alt="{{ $galleryCaption }}" @if (! $loop->first) loading="lazy" @endif />
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="swiper-button-next details-swiper-next"></div>
            <div class="swiper-button-prev details-swiper-prev"></div>
            <div class="swiper-pagination details-swiper-pagination"></div>
        </div>
    </div>
@endif
