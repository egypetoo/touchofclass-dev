<section class="km-why" id="why">
  <div class="km-why-bg" aria-hidden="true"></div>

  <div class="container position-relative">
    <div class="row g-4 g-xl-5 align-items-center">
      <div class="col-lg-6">
        <div class="km-eyebrow">
          <span class="km-eyebrow-text">{{ $homeWhy['eyebrow'] }}</span>
          <span class="km-eyebrow-line"></span>
        </div>
        <h2 class="km-why-title">
          {{ $homeWhy['lines'][0] ?? '' }}
          @if (filled($homeWhy['lines'][1] ?? null))
            <span class="text-km-gradient"><bdi>{{ $homeWhy['lines'][1] }}</bdi></span>
          @endif
        </h2>
        <p class="km-why-desc">
          {{ $homeWhy['text'] }}
        </p>

        <div class="km-why-features row g-3 g-sm-4">
          @foreach ($homeWhy['benefits'] as $benefit)
            <div class="col-sm-6">
              <div class="km-feature h-100">
                <span class="km-feature-icon"><i class="{{ $benefit['icon'] }}"></i></span>
                <div>
                  <h3>{{ $benefit['title'] }}</h3>
                  <p>{{ $benefit['text'] }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="col-lg-6">
        <div class="km-stats">
          <div class="row g-3 g-sm-4">
            @foreach ($homeWhy['stats'] as $stat)
              <div class="col-6">
                <div class="km-stat">
                  <strong>
                    @if ($stat['suffix'] !== '')
                      <span>{{ $stat['suffix'] }}</span>
                    @endif
                    <span class="km-counter" data-count="{{ $stat['number'] }}">0</span>
                  </strong>
                  <p>{{ $stat['text'] }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
