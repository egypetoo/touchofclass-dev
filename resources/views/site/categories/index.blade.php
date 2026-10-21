@extends('layouts.site')

@section('content')
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-1.webp') }}" width="1920" height="1080" alt=""
      aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">{{ __('site.our_categories') }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('site.products') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <section class="km-cats">
    <div class="container">
      <div class="row g-4 justify-content-center">
        @forelse ($categories as $category)
          @php
            $slug = $category->getTranslation('slug', 'en');
            $count = $category->products->count() + $category->children->sum(fn ($child) => $child->products->count());
            $image = $category->getFirstMediaUrl('categories') ?: asset('assets/images/'.match ($slug) {
                'deep-well-pumps' => 'products/pump-submersible.webp',
                'deep-well-motors' => 'products/motor-ie3.webp',
                'deep-well-pipes' => 'products/pipe-hdpe.webp',
                'submersible-cables' => 'products/cable-submersible.webp',
                'electrical-panels' => 'products/cable-power.webp',
                default => 'products/pump-submersible.webp',
            });
          @endphp
          <div class="col-12 col-sm-6 col-lg-4">
            <a class="km-cat-card" href="{{ route('products.index', ['category' => $slug]) }}">
              <span class="km-cat-card-media">
                <img src="{{ $image }}" width="864" height="648" alt="{{ $category->name }}" loading="lazy" decoding="async">
              </span>
              <span class="km-cat-card-body">
                <strong>{{ $category->name }}</strong>
                
                <em>{{ trans_choice('site.home_products_count', $count, ['count' => $count]) }}</em>
              </span>
            </a>
          </div>
        @empty
          <p class="text-center">{{ __('site.no_categories') }}</p>
        @endforelse
      </div>
    </div>
  </section>
@endsection
