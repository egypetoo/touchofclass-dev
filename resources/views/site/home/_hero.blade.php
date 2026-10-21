<section class="hero hero-slider-section position-relative d-none d-md-block" id="home" aria-label="عرض Touch of Class الرئيسي">
  <!-- Swiper Slider Container -->
  <div class="swiper hero-slider h-100">
    <div class="swiper-wrapper">

      <!-- Slide 1 -->
      <div class="swiper-slide hero-slide">
        <div class="hero-slide-bg"></div>
        <div class="container-fluid px-3 px-md-5 h-100">
          <div class="row min-vh-100 align-items-center">
            <div class="col-12 ">
              <div class="hero-content">
                
                <!-- Eyebrow with Crown -->
                <div class="hero-eyebrow d-inline-flex align-items-center gap-3 mb-3">
                  <img class="hero-mark" src="./assets/images/logo/logo-clear.png" alt="Touch of Class" width="240" height="177"/>
                </div>

                <!-- Main Heading -->
                <h1 class="hero-title">
                  <span class="d-block text-white">تصميم داخلي</span>
                  <span class="d-block hero-title-gold">بلمسة فخامة</span>
                </h1>

                <!-- Description -->
                <p class="hero-copy">
                  شركة تصميم داخلي تصمم مساحات سكنية وتجارية تجمع بين الجمال والوظيفة، من الفكرة حتى التنفيذ.
                </p>

                <!-- Action Buttons -->
                <div class="hero-actions d-flex align-items-center justify-content-start gap-3 flex-wrap">
                  <a class="btn btn-hero-gold" href="./projects.html">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>اطّلع على أعمالنا</span>
                  </a>
              
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="swiper-slide hero-slide" data-bg="./assets/images/studio/hero-2.jpg">
        <div class="hero-slide-bg"></div>
        <div class="container-fluid px-3 px-md-5 h-100">
          <div class="row min-vh-100 align-items-center">
            <div class="col-12">
              <div class="hero-content">
                
                <div class="hero-eyebrow d-inline-flex align-items-center gap-3 mb-3">
                  <img class="hero-mark" src="./assets/images/logo/logo-clear.png" alt="Touch of Class" width="240" height="177"/>
                </div>

                <h1 class="hero-title">
                  <span class="d-block text-white">تجديد يعيد للمكان</span>
                  <span class="d-block hero-title-gold">حياته وتفاصيله</span>
                </h1>

                <p class="hero-copy">
                  نحوّل المساحات القائمة لمكان معاصر يحافظ على طابع المبنى ويضيف راحة وأناقة في كل تفصيلة.
                </p>

                <div class="hero-actions d-flex align-items-center justify-content-start gap-3 flex-wrap">
                  <a class="btn btn-hero-gold" href="./projects.html">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>خدمات التجديد</span>
                  </a>
                  <a class="btn btn-hero-video" href="./contact.html">
                    <span class="btn-video-icon"><i class="fa-solid fa-envelope"></i></span>
                    <span>احجز استشارة</span>
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="swiper-slide hero-slide" data-bg="./assets/images/studio/hero-3.jpg">
        <div class="hero-slide-bg"></div>
        <div class="container-fluid px-3 px-md-5 h-100">
          <div class="row min-vh-100 align-items-center">
            <div class="col-12 ms-auto">
              <div class="hero-content">
                
                <div class="hero-eyebrow d-inline-flex align-items-center gap-3 mb-3">
                  <img class="hero-mark" src="./assets/images/logo/logo-clear.png" alt="Touch of Class" width="240" height="177"/>
                </div>

                <h1 class="hero-title">
                  <span class="d-block text-white">من المخطط حتى التسليم</span>
                  <span class="d-block hero-title-gold">إدارة مشروع كاملة</span>
                </h1>

                <p class="hero-copy">
                  نتابع التصميم والتنفيذ والجدول الزمني والتكلفة، حتى يُنجز المشروع بالجودة المطلوبة وفي موعده.
                </p>

                <div class="hero-actions d-flex align-items-center justify-content-start gap-3 flex-wrap">
                  <a class="btn btn-hero-gold" href="./services.html">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>تعرّف على خدماتنا</span>
                  </a>
                  <a class="btn btn-hero-video" href="./contact.html">
                    <span class="btn-video-icon"><i class="fa-solid fa-headset"></i></span>
                    <span>ابدأ مشروعك</span>
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

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
    <button type="button" class="hero-mobile-dot" data-index="1" aria-label="الشريحة 2"></button>
    <button type="button" class="hero-mobile-dot" data-index="2" aria-label="الشريحة 3"></button>
  </div>
</section>



<!-- Hero Mobile -->
<section class="hero hero-mobile d-md-none" aria-label="عرض Touch of Class الرئيسي">
  <div class="swiper hero-slider">
    <div class="swiper-wrapper">

      <!-- Slide 1 -->
      <div class="swiper-slide hero-slide">
        <div class="hero-mobile-media">
          <img
            class="hero-mobile-img"
            src="./assets/images/studio/hero-1.jpg"
            width="1920"
            height="1080"
            alt="تصميم داخلي من Touch of Class"
            decoding="async"
          >
        </div>
        <div class="hero-content">
          <div class="hero-eyebrow d-inline-flex align-items-center gap-2 mb-2">
            <img class="hero-mark" src="./assets/images/logo/logo-clear.png" alt="Touch of Class" width="240" height="177"/>
          </div>
          <h1 class="hero-title">
            <span class="d-block text-white">تصميم داخلي</span>
            <span class="d-block hero-title-gold">بلمسة فخامة</span>
          </h1>
          <p class="hero-copy">
            شركة تصميم داخلي تصمم مساحات سكنية وتجارية تجمع بين الجمال والوظيفة، من الفكرة حتى التنفيذ.
          </p>
          <div class="hero-actions d-flex align-items-center gap-2 flex-wrap">
            <a class="btn btn-hero-gold" href="./projects.html">
              <i class="fa-solid fa-arrow-left"></i>
              <span>اطّلع على أعمالنا</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="swiper-slide hero-slide">
        <div class="hero-mobile-media">
          <img
            class="hero-mobile-img"
            src="./assets/images/studio/hero-2.jpg"
            width="1920"
            height="1080"
            alt="تجديد داخلي من Touch of Class"
            loading="lazy"
            decoding="async"
          >
        </div>
        <div class="hero-content">
          <div class="hero-eyebrow d-inline-flex align-items-center gap-2 mb-2">
            <img class="hero-mark" src="./assets/images/logo/logo-clear.png" alt="Touch of Class" width="240" height="177"/>
          </div>
          <h1 class="hero-title">
            <span class="d-block text-white">تجديد يعيد للمكان</span>
            <span class="d-block hero-title-gold">حياته وتفاصيله</span>
          </h1>
          <p class="hero-copy">
            نحوّل المساحات القائمة لمكان معاصر يحافظ على طابع المبنى ويضيف راحة وأناقة في كل تفصيلة.
          </p>
          <div class="hero-actions d-flex align-items-center gap-2 flex-wrap">
            <a class="btn btn-hero-gold" href="./projects.html">
              <i class="fa-solid fa-arrow-left"></i>
              <span>خدمات التجديد</span>
            </a>
            <a class="btn btn-hero-video" href="./contact.html">
              <span class="btn-video-icon"><i class="fa-solid fa-envelope"></i></span>
              <span>احجز استشارة</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="swiper-slide hero-slide">
        <div class="hero-mobile-media">
          <img
            class="hero-mobile-img"
            src="./assets/images/studio/hero-3.jpg"
            width="1920"
            height="1080"
            alt="إدارة مشروع من Touch of Class"
            loading="lazy"
            decoding="async"
          >
        </div>
        <div class="hero-content">
          <div class="hero-eyebrow d-inline-flex align-items-center gap-2 mb-2">
            <img class="hero-mark" src="./assets/images/logo/logo-clear.png" alt="Touch of Class" width="240" height="177"/>
          </div>
          <h1 class="hero-title">
            <span class="d-block text-white">من المخطط حتى التسليم</span>
            <span class="d-block hero-title-gold">إدارة مشروع كاملة</span>
          </h1>
          <p class="hero-copy">
            نتابع التصميم والتنفيذ والجدول الزمني والتكلفة، حتى يُنجز المشروع بالجودة المطلوبة وفي موعده.
          </p>
          <div class="hero-actions d-flex align-items-center gap-2 flex-wrap">
            <a class="btn btn-hero-gold" href="./services.html">
              <i class="fa-solid fa-arrow-left"></i>
              <span>تعرّف على خدماتنا</span>
            </a>
            <a class="btn btn-hero-video" href="./contact.html">
              <span class="btn-video-icon"><i class="fa-solid fa-headset"></i></span>
              <span>ابدأ مشروعك</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="hero-mobile-nav">
    <button type="button" class="hero-mobile-dot active" data-index="0" aria-label="الشريحة 1"></button>
    <button type="button" class="hero-mobile-dot" data-index="1" aria-label="الشريحة 2"></button>
    <button type="button" class="hero-mobile-dot" data-index="2" aria-label="الشريحة 3"></button>
  </div>
</section>
