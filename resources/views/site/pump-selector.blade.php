@extends('layouts.site')

@section('content')
<!--
  ##################################################
  #                 Pump Selector                  #
  ##################################################
-->
  <section class="km-selector " id="pump-selector">
    <div class="km-selector-bg" aria-hidden="true">
      <img src="{{ asset('assets/images/pump-selector-bg.webp') }}" alt="" class="km-selector-bg-img" width="1920" height="1080" loading="lazy" decoding="async" />
    </div>

    <div class="container position-relative pt-5">
      <div class="row g-5 align-items-center km-selector-row">
        <div class="col-lg-7">
          <div class="km-selector-content">
            <div class="km-eyebrow">
              <span class="km-eyebrow-text">{{ $homeSelector?->sub_title ?: __('site.home_selector_eyebrow') }}</span>
              <span class="km-eyebrow-line"></span>
            </div>
            <h2 class="km-selector-title">
              @foreach (($homeSelector?->titleLines() ?: __('site.home_selector_lines')) as $line)
                @if ($loop->iteration === 2)<span class="text-km-gradient">{{ $line }}</span>@else {{ $line }} @endif
              @endforeach
            </h2>
            <p class="km-selector-desc">
              {{ $homeSelector?->text ? trim(strip_tags($homeSelector->text)) : __('site.home_selector_text') }}
            </p>

            <ul class="km-selector-features row g-0">
              @php
                $selectorIcons = ['fa-headset', 'fa-cubes', 'fa-regular fa-clock', 'fa-bullseye'];
                $selectorFallback = __('site.home_selector_features');
                $selectorFeatures = $homeSelectorFeatures->isNotEmpty()
                    ? $homeSelectorFeatures
                    : collect($selectorFallback)->map(fn (string $label) => (object) ['title' => $label]);
              @endphp
              @foreach ($selectorFeatures as $feature)
                <li class="col-6 col-sm-3">
                  <span class="km-selector-feature-icon"><i class="{{ str_contains($selectorIcons[$loop->index % 4], ' ') ? $selectorIcons[$loop->index % 4] : 'fa-solid '.$selectorIcons[$loop->index % 4] }}"></i></span>
                  <span>{{ $feature->title }}</span>
                </li>
              @endforeach
            </ul>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="km-selector-card">
            <div class="km-eyebrow justify-content-center">
              <span class="km-eyebrow-line"></span>
              <span class="km-eyebrow-text">{{ __('site.home_selector_card') }}</span>
              <span class="km-eyebrow-line"></span>
            </div>
            <h3 class="km-selector-card-title">{{ __('site.home_selector_duty') }}</h3>

            <form class="needs-validation" id="pump-selector-form" action="{{ route('pump-selector') }}#results" method="get" novalidate>
              <div class="km-selector-field">
                <label for="selector-flow" class="km-label">{{ __('site.home_flow') }} <span>*</span></label>
                <div class="km-selector-group">
                  <input type="number" id="selector-flow" name="flow" class="km-input" value="{{ request('flow') }}" placeholder="{{ __('site.home_flow_example') }}" min="0.01" step="any" inputmode="decimal" required />
                  <select id="selector-flow-unit" name="flow_unit" class="km-input km-select" aria-label="{{ __('site.home_flow_unit') }}">
                    <option value="m3h" @selected(request('flow_unit', 'm3h') === 'm3h')>{{ __('site.home_flow_m3h') }}</option>
                    <option value="ls" @selected(request('flow_unit') === 'ls')>{{ __('site.home_flow_ls') }}</option>
                    <option value="gpm" @selected(request('flow_unit') === 'gpm')>{{ __('site.home_flow_gpm') }}</option>
                  </select>
                  <span class="km-selector-group-icon"><i class="fa-solid fa-water"></i></span>
                </div>
              </div>

              <div class="km-selector-field">
                <label for="selector-head" class="km-label">{{ __('site.home_head') }} <span>*</span></label>
                <div class="km-selector-group">
                  <input type="number" id="selector-head" name="head" class="km-input" value="{{ request('head') }}" placeholder="{{ __('site.home_head_example') }}" min="0.01" step="any" inputmode="decimal" required />
                  <select id="selector-head-unit" name="head_unit" class="km-input km-select" aria-label="{{ __('site.home_head_unit') }}">
                    <option value="m" @selected(request('head_unit', 'm') === 'm')>{{ __('site.home_head_m') }}</option>
                    <option value="bar" @selected(request('head_unit') === 'bar')>{{ __('site.home_head_bar') }}</option>
                    <option value="ft" @selected(request('head_unit') === 'ft')>{{ __('site.home_head_ft') }}</option>
                  </select>
                  <span class="km-selector-group-icon"><i class="fa-solid fa-gauge-high"></i></span>
                </div>
              </div>

              <button type="submit" class="km-btn km-btn-block km-selector-submit">
                <span>{{ __('site.home_selector_submit') }}</span>
                <span class="km-btn-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
              </button>
            </form>

            <p class="km-selector-note">{{ __('site.home_selector_note') }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

    @if ($duty)
  <section class="km-result" id="results">
    <div class="container">
      <div class="km-result-stats row g-3 mb-4">
        <div class="col-sm-6">
          <div class="km-result-stat h-100">
            <span class="km-result-stat-label">{{ __('site.home_duty_flow') }}</span>
            <strong dir="ltr">{{ $duty['flow'] }} m³/h</strong>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="km-result-stat h-100">
            <span class="km-result-stat-label">{{ __('site.home_duty_head') }}</span>
            <strong dir="ltr">{{ $duty['head'] }} m</strong>
          </div>
        </div>
      </div>

      <p class="km-selector-note">{{ __('site.home_selector_head_note') }}</p>

      <h2 class="h4 mt-4">{{ __('site.home_selector_matches') }}</h2>
      @if ($matchedPumps->isEmpty())
        <p>{{ __('site.home_selector_none') }}</p>
      @else
        <div class="row g-3">
          @foreach ($matchedPumps as $product)
            <div class="col-md-6 col-lg-4">
              <article class="km-product-card h-100" data-match="{{ $product->code }}">
                <div class="km-product-body">
                  <h3 class="km-product-name"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                  <p dir="ltr">{{ $product->code }}</p>
                  @if ($product->short_description)
                    <p>{{ $product->short_description }}</p>
                  @endif
                </div>
              </article>
            </div>
          @endforeach
        </div>
      @endif

      @if ($unratedPumps->isNotEmpty())
        <h2 class="h4 mt-4">{{ __('site.home_selector_unrated') }}</h2>
        <ul>
          @foreach ($unratedPumps as $product)
            <li data-unrated="{{ $product->code }}"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a> <span dir="ltr">({{ $product->code }})</span></li>
          @endforeach
        </ul>
      @endif

      <form class="row g-3 needs-validation mt-4" action="{{ route('contact.store') }}" method="post">
        @csrf
        <input type="hidden" name="project_type" value="pump" />
        <div class="col-md-6">
          <label class="km-label" for="duty-name">{{ __('site.home_full_name') }} <span>*</span></label>
          <input class="km-input" id="duty-name" name="name" value="{{ old('name') }}" required />
        </div>
        <div class="col-md-6">
          <label class="km-label" for="duty-phone">{{ __('site.home_phone') }} <span>*</span></label>
          <input class="km-input" id="duty-phone" name="phone" value="{{ old('phone') }}" required />
        </div>
        <div class="col-12">
          <label class="km-label" for="duty-message">{{ __('site.home_message') }}</label>
          <textarea class="km-input" id="duty-message" name="message" rows="4">{{ old('message', __('site.home_duty_message', ['flow' => $duty['flow'], 'head' => $duty['head']])) }}</textarea>
        </div>
        <x-site.turnstile :enabled="$turnstileEnabled" :site-key="$turnstileSiteKey" />
        <div class="col-12">
          <button type="submit" class="km-btn"><span>{{ __('site.home_ask_engineer') }}</span></button>
        </div>
      </form>
    </div>
  </section>
  @endif

<!--
  ##################################################
  #                  Footer                        #
  ##################################################
-->
@endsection
