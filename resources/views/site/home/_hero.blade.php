<section class="hero hero-slider-section position-relative d-none d-md-block" id="home" aria-label="عرض Touch of Class الرئيسي">
  <!-- Swiper Slider Container -->
  <div class="swiper hero-slider h-100">
    <div class="swiper-wrapper">

      <!-- Slide 1 -->
      @foreach ($heroSlides as $image)
        <div class="swiper-slide hero-slide">
          <div class="hero-slide-bg" @if ($loop->first) style="--slide-bg: url('{{ $image['image'] }}')" @else data-bg="{{ $image['image'] }}" @endif></div>
          <div class="container-fluid px-3 px-md-5 h-100">
            <div class="row min-vh-100 align-items-center">
              <div class="col-12 ">
                <div class="hero-content">

                  <!-- Eyebrow with Crown -->
                  <div class="hero-eyebrow d-inline-flex align-items-center gap-3 mb-3">
                    <img class="hero-mark" src="{{ $siteLogoUrl ?? asset('assets/images/logo/logo-clear.png') }}" alt="Touch of Class" width="240" height="177"/>  
                  </div>

                  <!-- Main Heading -->
                  <h1 class="hero-title">
                    <span class="d-block text-white">{{ $image['lines'][0] ?? '' }}</span>
                    <span class="d-block hero-title-gold">{{ $image['lines'][1] ?? '' }}</span>
                  </h1>

                  <!-- Description -->
                  <p class="hero-copy">
                    {{ $image['text'] }}
                  </p>

                  <!-- Action Buttons -->
                  <div class="hero-actions d-flex align-items-center justify-content-start gap-3 flex-wrap">
                    <a class="btn btn-hero-gold" href="{{ $image['url'] }}">
                      <i class="fa-solid fa-arrow-left"></i>
                      <span>{{ $image['button'] }}</span>
                    </a>

                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Navigation Arrows (Left/Right) -->
  <div class="hero-slider-nav d-none d-md-flex align-items-center gap-2">
    <button type="button" class="hero-nav-arrow hero-prev" aria-label="السابق">
      <i class="fa-solid fa-angle-right"></i>
    </button>
    <button type="button" class="hero-nav-arrow hero-next" aria-label="التالي">
      <i class="fa-solid fa-angle-left"></i>
    </button>
  </div>

  <!-- Side Index with Clickable Dots and Animated Bar -->
  <div class="hero-index" aria-label="التنقل بين الشرائح">
    <span class="hero-index-num">01</span>
    <div class="hero-index-track">
      <i class="hero-index-bar"></i>
      <span class="hero-index-dot active" data-index="0" title="شريحة 1"></span>
      <span class="hero-index-dot" data-index="1" title="شريحة 2"></span>
      <span class="hero-index-dot" data-index="2" title="شريحة 3"></span>
    </div>
    <small class="hero-index-total">03</small>
  </div>

  <!-- Mobile Pagination Dots -->
  <div class="hero-mobile-nav">
    <button type="button" class="hero-mobile-dot active" data-index="0" aria-label="الشريحة 1"></button>
    @foreach ($heroSlides as $image)
      <button type="button" class="hero-mobile-dot" data-index="{{ $loop->index }}" aria-label="الشريحة  {{ $loop->index + 1 }}"></button>
    @endforeach
  </div>
</section>



<!-- Hero Mobile -->
<section class="hero hero-mobile d-md-none" aria-label="عرض Touch of Class الرئيسي">
  <div class="swiper hero-slider">
    <div class="swiper-wrapper">

      <!-- Slide 1 -->
      @foreach ($heroSlides as $image)
        <div class="swiper-slide hero-slide">
          <div class="hero-mobile-media">
            <img
              class="hero-mobile-img"
              src="{{ $image['imageMobile'] }}"
              srcset="{{ $image['imageMobile'] }} 960w, {{ $image['image'] }} 1600w"
              sizes="100vw"
              @if ($loop->first)
                fetchpriority="high"
              @else
                loading="lazy"
              @endif
              width="960"
              height="540"
              alt="{{ $image['alt'] }}"
              decoding="async"
            >
          </div>
          <div class="hero-content">
            <div class="hero-eyebrow d-inline-flex align-items-center gap-2 mb-2">
              <img class="hero-mark" src="{{ $siteLogoUrl ?? asset('assets/images/logo/logo-clear.png') }}" alt="{{ $siteDisplayName }}" width="240" height="177"/>
            </div>
            <h1 class="hero-title">
              <span class="d-block text-white">{{ $image['lines'][0] ?? '' }}</span>
              <span class="d-block hero-title-gold">{{ $image['lines'][1] ?? '' }}</span>
            </h1>
            <p class="hero-copy">
              {!! empty($image['text']) ? __('site.hero_description') : $image['text'] !!}
            </p>
            <div class="hero-actions d-flex align-items-center gap-2 flex-wrap">
              <a class="btn btn-hero-gold" href="{{ $image['url'] }}">
                <i class="fa-solid fa-arrow-left"></i>
                <span>{{ $image['button'] }}</span>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="hero-mobile-nav">
    @foreach ($heroSlides as $image)
      <button type="button" class="hero-mobile-dot active" data-index="{{ $loop->index }}" aria-label="الشريحة  {{ $loop->index + 1 }}"></button>
    @endforeach
  </div>
</section>
