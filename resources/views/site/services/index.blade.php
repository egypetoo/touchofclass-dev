@extends('layouts.site')

@section('content')
<!--
  ##################################################
  #                   Page Header                  #
  ##################################################
-->
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-1.webp') }}" width="1920" height="1080" alt=""
      aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">{{ __('site.services') }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('site.services') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!--
  ##################################################
  #                   Services                     #
  ##################################################
-->
  <section class="services-km">
    <div class="container position-relative z-2">

  

      <div class="row g-3 justify-content-center">
        @foreach ($serviceCards as $card)
          <div class="col-12 col-sm-6 col-lg-3">
            <article class="service-card">
              <div class="service-card-media">
                <img src="{{ $card->imageUrl }}" width="864" height="1152" alt="{{ $card->title }}" class="service-card-img" loading="lazy" decoding="async">
              </div>
              <div class="service-card-body">
                <h3 class="service-card-title">{{ $card->title }}</h3>
                @if ($card->excerpt !== '')
                  <p class="service-card-desc">{{ $card->excerpt }}</p>
                @endif
                <div class="service-card-action">
                  <a href="{{ $card->url }}" class="service-card-btn" aria-label="{{ $card->title }}">
                    <i class="fa-solid fa-arrow-left"></i>
                  </a>
                </div>
              </div>
            </article>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!--
  ##################################################
  #                  Footer                        #
  ##################################################
-->
@endsection
