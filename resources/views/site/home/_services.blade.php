 <!--
      ##################################################
      #                   Services                     #
      ##################################################
    -->
    <section class="services-km" id="services">
        <!-- Decorative background -->
    
    
        <div class="container position-relative z-2">
    
          <!-- Header -->
          <div class="services-km-head text-center">
            <div class="services-km-eyebrow d-inline-flex align-items-center gap-3 mb-3">
              <span class="services-km-eyebrow-line"></span>
              <span class="services-km-eyebrow-text">{{ $homeServicesIntro?->sub_title ?: __('site.services_title') }}</span>   
              <span class="services-km-eyebrow-line"></span>
            </div>
    
            <h2 class="services-km-title mb-3">
              <span class="services-km-title-dark">{{ $homeServicesIntro?->titleLines()[0] ?? __('site.services_title') }}</span>
              <span class="services-km-title-gold">{{ filled($homeServicesIntro?->second_title) ? $homeServicesIntro->second_title : ($homeServicesIntro?->titleLines()[1] ?? '') }}</span>
            </h2>
    
            <p class="services-km-desc mx-auto">
              {!! empty($homeServicesIntro->text) ? __('site.services_description') : $homeServicesIntro->text !!}
            </p>
          </div>
    
          <div class="services-km-slider">
            <div class="swiper services-swiper" aria-label="{{ $homeServicesIntro->title ?? __('site.services_title') }}">
              <div class="swiper-wrapper">
    
                <!-- 1. التصميم الداخلي -->
                @foreach ($homeServices as $service)
                  <div class="swiper-slide">
                    <article class="service-card">
                      <div class="service-card-media">
                        <img src="{{ $service->mainImageUrl() }}" sizes="(max-width: 575px) 92vw, (max-width: 991px) 46vw, 440px" width="864" height="1152" alt="{{ $service->title }} - {{ $siteDisplayName }}" class="service-card-img" loading="lazy" decoding="async">
                      </div>
    
                      <div class="service-card-body">
                        <h3 class="service-card-title">{{ $service->title }}</h3>
                        <p class="service-card-desc">
                          {!! empty($service->short_text) ? __('site.service_description') : $service->short_text !!}
                        </p>
                        <div class="service-card-action">
                          <a href="{{ route('services.show', $service) }}" class="service-card-btn" aria-label="تعرف على تفاصيل خدمة {{ $service->title }}">
                            <i class="fa-solid fa-arrow-left"></i>
                          </a>
                        </div>
                      </div>
                    </article>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="services-km-nav">
              <button type="button" class="services-km-prev" aria-label="السابق"><i class="fa-solid fa-arrow-right"></i></button>
              <button type="button" class="services-km-next" aria-label="التالي"><i class="fa-solid fa-arrow-left"></i></button>
            </div>
          </div>
    
          <div class="km-section-more" data-anim="up">
            <a href="{{ route('services.index') }}" class="km-btn">
              <span>{{ __('site.show_all_services') }}</span>
              <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
            </a>
          </div>
        </div>
      </section>