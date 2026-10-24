 <!--
      ##################################################
      #                   About us                     #
      ##################################################
    -->
    <section class="about about-km position-relative overflow-hidden py-5" id="about">
        <!-- Decorative background -->
        <div class="about-km-bg" aria-hidden="true">
          <span class="about-km-shape about-km-shape-1"></span>
          <span class="about-km-shape about-km-shape-2"></span>
          <span class="about-km-panel"></span>
          <span class="about-km-panel-line"></span>
          <span class="about-km-frame about-km-frame-top"></span>
          <span class="about-km-frame about-km-frame-bottom"></span>
        </div>
    
        <div class="container h-100 position-relative z-2 py-5">
          <div class="row align-items-center">
            <div class="col-12 col-lg-5 order-lg-2">
              <div class="about-content">
    
                <!-- Eyebrow -->
                <div class="about-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                  <span class="about-eyebrow-line"></span>
                  <span class="about-eyebrow-text">{{ $homeAbout['eyebrow'] }}</span>
                </div>
    
                <!-- Main Heading -->
                <h2 class="about-title mb-3">
                  <span class="d-block about-title-dark">{{ $homeAbout['lines'][0] ?? '' }}</span>
                  <span class="d-block about-title-gold">{{ $homeAbout['lines'][1] ?? '' }}</span>
                </h2>
    
                <!-- Description -->
                <p class="about-desc mb-4">
                  {{ $homeAbout['text'] }}
                </p>
    
                <!-- Action Button -->
                <div class="about-action">
                  <a href="{{ $homeAbout['url'] }}" class="km-btn">
                    <span>{{ $homeAbout['button'] }}</span>
                    <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
                  </a>
                </div>
    
              </div>
            </div>
    
            <!-- Image Cards -->
            <div class="col-12 col-lg-7">
              <div class="about-cards d-flex flex-column flex-lg-row">
                <figure class="about-card about-card-pivot">
                  <div class="about-card-media" data-anim="reveal" data-anim-delay="0.25">
                    <img
                      src="{{ $homeAbout['images'][0] }}"
                      sizes="(max-width: 991px) 45vw, 380px"
                      width="800"
                      height="1067"
                      alt="{{ $homeAbout['alt'] }}"
                      loading="lazy"
                      decoding="async"
                    >
                  </div>
                </figure>
    
                <figure class="about-card about-card-pump">
                  <div class="about-card-media" data-anim="reveal">
                    <img
                      src="{{ $homeAbout['images'][1] }}"
                      sizes="(max-width: 991px) 45vw, 380px"
                      width="800"
                      height="1067"
                      alt="{{ $homeAbout['alt'] }}"
                      loading="lazy"
                      decoding="async"
                    >
                  </div>
                </figure>
              </div>
            </div>
    
          </div>
        </div>
      </section>