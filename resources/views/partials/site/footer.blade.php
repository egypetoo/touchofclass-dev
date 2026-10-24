<div class="km-lightbox" id="km-lightbox" hidden>
  <button type="button" class="km-lightbox-close" aria-label="{{ __('site.close') }}"><i class="fa-solid fa-xmark"></i></button>
  <button type="button" class="km-lightbox-nav km-lightbox-prev" aria-label="{{ __('site.previous') }}"><i class="fa-solid fa-angle-right"></i></button>
  <img alt="">
  <button type="button" class="km-lightbox-nav km-lightbox-next" aria-label="{{ __('site.next') }}"><i class="fa-solid fa-angle-left"></i></button>
  <p class="km-lightbox-caption"></p>
</div>

<footer id="footer" class="footer km-footer">
  <div class="km-footer-inner">
    <div class="km-footer-deco" aria-hidden="true">
      <svg class="km-footer-top-curve" viewBox="0 0 1600 40" fill="none" preserveAspectRatio="none">
        <path d="M0,0 H1600 C1150,34 450,34 0,6 Z" fill="#f0f1f1"/>
      </svg>
    </div>

    <div class="km-footer-container">
      <div class="km-footer-top row gx-3 gx-sm-4 gy-4 gy-md-5">
        <div class="km-footer-brand col-12 col-lg-3">
          <a href="{{ $siteFooter['home'] }}" class="km-footer-logo" aria-label="{{ $siteFooter['name'] }}">
            <img
              src="{{ $siteFooter['logo'] }}"
              alt="{{ $siteFooter['name'] }}"
              width="305"
              height="141"
              loading="lazy"
              decoding="async"
            />
          </a>
          @if ($siteFooter['description'] !== '')
            <p class="km-footer-desc">{{ $siteFooter['description'] }}</p>
          @endif
          @if ($siteFooter['socials'] !== [])
            <div class="km-footer-social">
              @foreach ($siteFooter['socials'] as $social)
                <a href="{{ $social['url'] }}" aria-label="{{ $social['label'] }}" @if ($social['external']) target="_blank" rel="noopener" @endif>
                  <i class="{{ $social['icon'] }}"></i>
                </a>
              @endforeach
            </div>
          @endif
        </div>

        <div class="km-footer-col col-6 col-md-4 col-lg">
          <h5 class="km-footer-title"><a href="{{ $siteFooter['projects']['url'] }}">{{ $siteFooter['projects']['title'] }}</a></h5>
          <ul class="km-footer-list">
            @foreach ($siteFooter['projects']['links'] as $link)
              <li><a href="{{ $link['url'] }}"><i class="fa-solid fa-angle-left"></i><span>{{ $link['title'] }}</span></a></li>
            @endforeach
          </ul>
        </div>

        @if ($siteFooter['services']['links'] !== [])
          <div class="km-footer-col col-6 col-md-4 col-lg">
            <h5 class="km-footer-title"><a href="{{ $siteFooter['services']['url'] }}">{{ $siteFooter['services']['title'] }}</a></h5>
            <ul class="km-footer-list">
              @foreach ($siteFooter['services']['links'] as $link)
                <li><a href="{{ $link['url'] }}"><i class="fa-solid fa-angle-left"></i><span>{{ $link['title'] }}</span></a></li>
              @endforeach
            </ul>
          </div>
        @endif

        @if ($siteFooter['information']['links'] !== [])
          <div class="km-footer-col col-12 col-md-4 col-lg">
            <h5 class="km-footer-title">
              @if ($siteFooter['information']['url'] !== '')
                <a href="{{ $siteFooter['information']['url'] }}">{{ $siteFooter['information']['title'] }}</a>
              @else
                {{ $siteFooter['information']['title'] }}
              @endif
            </h5>
            <ul class="km-footer-list">
              @foreach ($siteFooter['information']['links'] as $link)
                <li><a href="{{ $link['url'] }}"><i class="fa-solid fa-angle-left"></i><span>{{ $link['title'] }}</span></a></li>
              @endforeach
            </ul>
          </div>
        @endif

        @if ($siteFooter['contact']['items'] !== [])
          <div class="km-footer-col km-footer-col-contact col-12 col-lg-3">
            <h5 class="km-footer-title"><a href="{{ $siteFooter['contact']['url'] }}">{{ $siteFooter['contact']['title'] }}</a></h5>
            <ul class="km-footer-contact row g-3 g-md-4 g-lg-3">
              @foreach ($siteFooter['contact']['items'] as $item)
                <li class="col-6 col-lg-12">
                  <span class="km-footer-contact-icon {{ $item['gold'] ? 'is-gold' : '' }}"><i class="{{ $item['icon'] }}"></i></span>
                  <div class="km-footer-contact-data">
                    @if ($item['href'])
                      <a href="{{ $item['href'] }}" @if ($item['ltr']) dir="ltr" @endif @if ($item['external']) target="_blank" rel="noopener" @endif>
                        @foreach ($item['lines'] as $line)
                          <span>{{ $line }}</span>
                        @endforeach
                      </a>
                    @else
                      <p>
                        @foreach ($item['lines'] as $line)
                          <span>{{ $line }}</span>
                        @endforeach
                      </p>
                    @endif
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>
    </div>

    <div class="km-footer-container">
      <div class="km-footer-bottom">
        <p class="km-footer-copyright">{{ $siteFooter['copyright'] }}</p>
      </div>
    </div>
  </div>

  @if ($siteFooter['phone'] || $siteFooter['whatsapp'])
    <div class="social-icon position-fixed d-flex flex-column gap-2">
      @if ($siteFooter['phone'])
        <a href="{{ $siteFooter['phone']['href'] }}" class="social-phone d-flex align-items-center justify-content-center" aria-label="{{ $siteFooter['phone']['label'] }}">
          <i class="fas fa-phone"></i>
        </a>
      @endif
      @if ($siteFooter['whatsapp'])
        <a href="{{ $siteFooter['whatsapp']['href'] }}" target="_blank" rel="noopener" class="social-whatsapp d-flex align-items-center justify-content-center" aria-label="{{ $siteFooter['whatsapp']['label'] }}">
          <i class="fab fa-whatsapp"></i>
        </a>
      @endif
    </div>
  @endif
</footer>

<div class="back-to-top" id="back-top">
  <button type="button" class="d-flex justify-content-center align-items-center" aria-label="{{ __('site.back_to_top') }}">
    <i class="fas fa-chevron-up"></i>
  </button>
</div>
