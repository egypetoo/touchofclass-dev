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
      <h1 class="km-page-title" data-anim="up">{{ $breadcrumb?->title ?: __('site.about_page_title') }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb?->title ?: __('site.about_page_title') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!--
  ##################################################
  #                   About Section                #
  ##################################################
-->
  <section class="km-about-block">
    <div class="container">
      <div class="row align-items-center g-4 g-lg-5">
        <div class="col-lg-6">
          <div class="km-about-figure">
            <div class="km-about-image" data-anim="reveal">
              <img src="{{ $about_us?->imageUrl('about/about-card-pump.webp') ?? asset('assets/images/about/about-card-pump.webp') }}"
                width="800" height="1067"
                alt="{{ $about_us?->alt_image ?: __('site.about_page_title') }}" loading="lazy" decoding="async" />
            </div>
           
          </div>
        </div>
        <div class="col-lg-6">
          <div class="km-about-text" data-anim="up">
            <div class="km-eyebrow">
              <span class="km-eyebrow-text">{{ $about_us?->sub_title ?: __('site.about_who') }}</span>
              <span class="km-eyebrow-line"></span>
            </div>
            <h2>
              @foreach (($about_us?->titleLines() ?: __('site.about_title')) as $line)
                @if ($loop->last)<span class="text-km-gradient">{{ $line }}</span>@else {{ $line }} @endif
              @endforeach
            </h2>
            @if ($about_us?->text)
              {!! $about_us->text !!}
            @else
              <p>{{ __('site.about_paragraph_1') }}</p>
              <p>{{ __('site.about_paragraph_2') }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--
  ##################################################
  #                   Vision Section               #
  ##################################################
-->
  <section class="km-about-block is-dark">
    <div class="container">
      <div class="row align-items-center g-4 g-lg-5 flex-row-reverse">
        <div class="col-lg-6">
          <div class="km-about-figure is-flip">
            <div class="km-about-image" data-anim="reveal">
              <img src="{{ $vision?->imageUrl('services/service-supply.webp') ?? asset('assets/images/services/service-supply.webp') }}"
                width="800" height="1067"
                alt="{{ $vision?->alt_image ?: __('site.about_vision') }}" loading="lazy" decoding="async" />
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="km-about-text" data-anim="up">
            <div class="km-eyebrow">
              <span class="km-eyebrow-text">{{ $vision?->sub_title ?: __('site.about_vision') }}</span>
              <span class="km-eyebrow-line"></span>
            </div>
            <h2>
              @foreach (($vision?->titleLines() ?: __('site.about_vision_title')) as $line)
                @if ($loop->last)<span class="text-km-gradient">{{ $line }}</span>@else {{ $line }} @endif
              @endforeach
            </h2>
            @if ($vision?->text)
              {!! $vision->text !!}
            @else
              <ul class="km-about-list">
                @foreach (__('site.about_vision_items') as $item)
                  <li><i class="fa-solid fa-check"></i><span>{{ $item }}</span></li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--
  ##################################################
  #                   Mission Section              #
  ##################################################
-->
  <section class="km-about-block">
    <div class="container">
      <div class="row align-items-center g-4 g-lg-5">
        <div class="col-lg-6">
          <div class="km-about-figure">
            <div class="km-about-image" data-anim="reveal">
              <img src="{{ $mission?->imageUrl('about/about-card-pivot.webp') ?? asset('assets/images/about/about-card-pivot.webp') }}"
                width="800" height="1067"
                alt="{{ $mission?->alt_image ?: __('site.about_mission') }}" loading="lazy" decoding="async" />
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="km-about-text" data-anim="up">
            <div class="km-eyebrow">
              <span class="km-eyebrow-text">{{ $mission?->sub_title ?: __('site.about_mission') }}</span>
              <span class="km-eyebrow-line"></span>
            </div>
            <h2>
              @foreach (($mission?->titleLines() ?: __('site.about_mission_title')) as $line)
                @if ($loop->last)<span class="text-km-gradient">{{ $line }}</span>@else {{ $line }} @endif
              @endforeach
            </h2>
            @if ($mission?->text)
              {!! $mission->text !!}
            @else
              <ul class="km-about-list">
                @foreach (__('site.about_mission_items') as $item)
                  <li><i class="fa-solid fa-check"></i><span>{{ $item }}</span></li>
                @endforeach
              </ul>
              <p>{{ __('site.about_mission_text') }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  @if (($founders ?? []) !== [])
    <section class="km-about-block">
      <div class="container">
        <div class="row justify-content-center g-4 g-lg-5">
          @foreach ($founders as $founder)
            <div class="col-10 col-sm-6 col-lg-4 text-center">
              @if ($founder['image'])
                <div class="km-about-figure">
                  <div class="km-about-image">
                    <img src="{{ $founder['image'] }}" alt="{{ $founder['name'] }}" width="600" height="800" loading="lazy" decoding="async">
                  </div>
                </div>
              @endif
              <h3 class="h5 mt-3 mb-1">{{ $founder['name'] }}</h3>
              <p class="mb-0">{{ $founder['title'] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  <!--
  ##################################################
  #                  Footer                        #
  ##################################################
-->
@endsection
