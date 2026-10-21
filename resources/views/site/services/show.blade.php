@extends('layouts.site')

@php
    $serviceFallbacks = [
        'technical-consulting' => 'service-consulting.webp',
        'spare-parts' => 'service-spareparts.webp',
        'warranty-maintenance' => 'service-maintenance.webp',
        'supply-and-installation' => 'service-supply.webp',
    ];
    $serviceFile = $serviceFallbacks[$service->getTranslation('slug', 'en')] ?? 'service-consulting.webp';
    $serviceImage = $service->mainImageUrl() ?: asset('assets/images/services/'.$serviceFile);
@endphp

@section('content')
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-3.webp') }}" width="1920" height="1080" alt=""
      aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">{{ $service->title }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('site.services') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $service->title }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <section class="km-sd">
    <div class="container">
      <div class="row g-4 g-xl-5">
        <div class="col-lg-8">
          <article class="km-sd-content">
            <div class="km-sd-media" data-anim="up">
              <img class="km-sd-media-main" src="{{ $serviceImage }}" width="864" height="1152" alt="{{ $service->title }}" decoding="async" />
            </div>

            <div class="km-sd-intro" data-anim="up">
              <div class="km-eyebrow">
                <span class="km-eyebrow-text">{{ __('site.service_about') }}</span>
                <span class="km-eyebrow-line"></span>
              </div>
              <h2>{{ $service->title }}</h2>
              @if ($service->text)
                <div class="km-prose">{!! $service->text !!}</div>
              @elseif ($service->short_text)
                <p class="km-prose-lead">{{ $service->short_text }}</p>
              @endif
            </div>
          </article>
        </div>

        <aside class="col-lg-4">
          <div class="km-sd-sidebar">
            <div class="km-widget">
              <h2 class="km-filter-title">{{ __('site.services') }}</h2>
              <ul class="km-sd-nav">
                @foreach ($related_services as $related)
                  @php
                    $relatedFile = $serviceFallbacks[$related->getTranslation('slug', 'en')] ?? 'service-consulting.webp';
                    $relatedImage = $related->mainImageUrl() ?: asset('assets/images/services/'.str_replace('.webp', '-480.webp', $relatedFile));
                  @endphp
                  <li>
                    <a href="{{ route('services.show', $related) }}" @class(['active' => $related->is($service)]) @if ($related->is($service)) aria-current="page" @endif>
                      <img src="{{ $relatedImage }}" width="480" height="640" alt="" loading="lazy" decoding="async" />
                      <span>{{ $related->title }}</span><i class="fa-solid fa-angle-left"></i>
                    </a>
                  </li>
                @endforeach
              </ul>
            </div>

            <div class="km-sd-help">
              <img class="km-sd-help-bg" src="{{ asset('assets/images/services/'.str_replace('.webp', '-480.webp', $serviceFile)) }}" width="480" height="640" alt="" aria-hidden="true" loading="lazy" decoding="async" />
              <h2>{{ __('site.service_request') }}</h2>
              <p>{{ __('site.service_request_text') }}</p>
              <form class="km-sd-help-form needs-validation" action="{{ route('contact.store') }}" method="post" novalidate>
                @csrf
                <x-site.utm-fields />
                <input type="hidden" name="service_id" value="{{ $service->id }}" />
                <div>
                  <label for="sd-name" class="km-label">{{ __('site.home_full_name') }} <span>*</span></label>
                  <div class="km-contact-field">
                    <input type="text" id="sd-name" name="name" value="{{ old('name') }}" class="km-input" placeholder="{{ __('site.home_name_placeholder') }}" autocomplete="name" required />
                    <i class="fa-regular fa-user"></i>
                  </div>
                </div>
                <div>
                  <label for="sd-phone" class="km-label">{{ __('site.home_phone') }} <span>*</span></label>
                  <div class="km-contact-field">
                    <input type="tel" id="sd-phone" name="phone" value="{{ old('phone') }}" class="km-input" placeholder="{{ __('site.home_phone_placeholder') }}" autocomplete="tel" required />
                    <i class="fa-solid fa-phone"></i>
                  </div>
                </div>
                <div>
                  <label for="sd-message" class="km-label">{{ __('site.service_details') }}</label>
                  <div class="km-contact-field is-textarea">
                    <textarea id="sd-message" name="message" class="km-input" rows="3" placeholder="{{ __('site.service_details_placeholder') }}">{{ old('message') }}</textarea>
                    <i class="fa-regular fa-message"></i>
                  </div>
                </div>
                <x-site.turnstile :enabled="$turnstileEnabled" :site-key="$turnstileSiteKey" />
                <button type="submit" class="km-btn km-btn-block">
                  <span>{{ __('site.service_submit') }}</span>
                  <span class="km-btn-icon"><i class="fa-regular fa-paper-plane"></i></span>
                </button>
              </form>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>
@endsection
