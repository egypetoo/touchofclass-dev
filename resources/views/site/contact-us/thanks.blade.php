@extends('layouts.site')

@section('content')
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-1.webp') }}" width="1920" height="1080" alt="" aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">{{ __('site.thank_you') }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('site.thank_you') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="text-center mx-auto" style="max-width: 40rem;">
        <p class="mb-4">{{ __('site.contact_message_sent') }}</p>
        <a href="{{ route('home') }}" class="km-btn">
          <span>{{ __('site.home') }}</span>
          <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
        </a>
      </div>
    </div>
  </section>
@endsection
