@extends('layouts.site')

@section('content')
<!--
  ##################################################
  #                   Page Header                  #
  ##################################################
-->
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-2.webp') }}" width="1920" height="1080" alt=""
      aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">{{ $product->name }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('site.products') }}</a></li>
          @if ($product->category)
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->getTranslation('slug', 'en')]) }}">{{ $product->category->name }}</a></li>
          @endif
          <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!--
  ##################################################
  #                    Product                     #
  ##################################################
-->
  <section class="km-pd">
    <div class="container">
      <div class="row g-4 g-xl-5">
        <div class="col-lg-6">
          <div class="km-pd-gallery" data-anim="up">
            <div class="km-pd-main">
              <img src="{{ $product->mainImageUrl() }}" width="600" height="450" alt="{{ $product->name }}" decoding="async" data-pd-main />
              <span class="km-pd-badge"><i class="fa-solid fa-shield-halved"></i> {{ __('site.product_genuine') }}</span>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="km-pd-info" data-anim="up">
            @php
              $specs = $product->specifications ?? [];
              $specOptions = $specs['options'] ?? [];
            @endphp
            <div class="km-pd-top">
              <span class="km-cart-brand">{{ $specs['brand'] ?? $product->name }}</span>
              <span class="km-pd-stock"><i class="fa-solid fa-circle-check"></i> {{ __('site.product_available') }}</span>
            </div>
            <h2 class="km-pd-title">{{ $product->name }}</h2>
            <p class="km-pd-model">
              @if (! empty($specs['model']))
                {{ __('site.product_model') }}: <span dir="ltr">{{ $specs['model'] }}</span> ·
              @endif
              {{ __('site.product_code') }}: <span dir="ltr">{{ $product->code }}</span>
            </p>
            @if (! empty($specs['summary']))
              <p class="km-pd-desc">{{ $product->specText($specs['summary']) }}</p>
            @endif

            @if (! empty($specs['facts']))
              <ul class="km-pd-facts row g-2">
                @foreach ($specs['facts'] as $fact)
                  <li class="col-6 col-sm-3">
                    <div class="km-pd-fact h-100">
                      <i class="fa-solid {{ $fact['icon'] ?? 'fa-circle-info' }}"></i>
                      <span>{{ $product->specText($fact['label'] ?? '') }}</span>
                      <strong dir="ltr">{{ $fact['value'] ?? '' }}</strong>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif

            @if ($specOptions !== [])
              <div class="km-pd-option">
                <span class="km-label">{{ $product->specText($specs['options_label'] ?? ['ar' => 'القدرة', 'en' => 'Power']) }}</span>
                <div class="km-pd-powers">
                  @foreach ($specOptions as $option)
                    <span dir="ltr">{{ $option }}</span>
                  @endforeach
                </div>
              </div>
            @endif

            <form class="km-pd-buy" action="{{ route('cart.store') }}" method="post">
              @csrf
              <input type="hidden" name="product_id" value="{{ $product->id }}" />
              <div class="km-qty">
                <button type="button" class="km-qty-btn" data-qty="-1" aria-label="{{ __('site.product_decrease_qty') }}"><i class="fa-solid fa-minus"></i></button>
                <input type="number" class="km-qty-input" name="qty" value="1" min="1" max="99" aria-label="{{ __('site.product_quantity') }}" />
                <button type="button" class="km-qty-btn" data-qty="1" aria-label="{{ __('site.product_increase_qty') }}"><i class="fa-solid fa-plus"></i></button>
              </div>
              <button type="submit" class="km-btn ">
                <span class="km-btn-icon"><i class="fa-solid fa-cart-plus"></i></span>
                <span>{{ __('site.home_add_to_cart') }}</span>
              </button>
              @php
                $quoteBase = $whatsappUrl ?: 'https://wa.me/201001234567';
                $quoteUrl = $quoteBase.(str_contains($quoteBase, '?') ? '&' : '?').'text='.rawurlencode(__('site.product_quote_text', ['name' => $product->name]));
              @endphp
              <a class="km-pd-wa" href="{{ $quoteUrl }}" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp"></i>
                <span>{{ __('site.product_request_quote') }}</span>
              </a>
            </form>

            <ul class="km-cart-perks">
              <li><i class="fa-solid fa-shield-halved"></i> {{ __('site.product_warranty') }}</li>
              <li><i class="fa-solid fa-truck-fast"></i> {{ __('site.product_delivery') }}</li>
              <li><i class="fa-solid fa-headset"></i> {{ __('site.product_install') }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="km-pd-tabs" data-anim="up">
        <ul class="nav km-pd-tabs-nav" role="tablist">
          <li role="presentation">
            <button class="active" id="tab-specs" data-bs-toggle="tab" data-bs-target="#pane-specs" type="button" role="tab" aria-controls="pane-specs" aria-selected="true">
              <i class="fa-solid fa-table-list"></i> {{ __('site.product_specs') }}
            </button>
          </li>
          <li role="presentation">
            <button id="tab-desc" data-bs-toggle="tab" data-bs-target="#pane-desc" type="button" role="tab" aria-controls="pane-desc" aria-selected="false">
              <i class="fa-regular fa-file-lines"></i> {{ __('site.description') }}
            </button>
          </li>
          <li role="presentation">
            <button id="tab-files" data-bs-toggle="tab" data-bs-target="#pane-files" type="button" role="tab" aria-controls="pane-files" aria-selected="false">
              <i class="fa-solid fa-download"></i> {{ __('site.product_downloads') }}
            </button>
          </li>
        </ul>

        <div class="tab-content km-pd-panes">
          <div class="tab-pane fade show active" id="pane-specs" role="tabpanel" aria-labelledby="tab-specs" tabindex="0">
            <div class="table-responsive">
              <table class="km-pd-table">
                <tbody>
                  @forelse ($specs['rows'] ?? [] as $row)
                    <tr>
                      <th scope="row">{{ $product->specText($row['label'] ?? '') }}</th>
                      <td dir="ltr">{{ $product->specText($row['value'] ?? '') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td>{{ __('site.product_no_specs') }}</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <div class="tab-pane fade" id="pane-desc" role="tabpanel" aria-labelledby="tab-desc" tabindex="0">
            <div class="km-prose">
              @if (! empty($specs['summary']))
                <p>{{ $product->specText($specs['summary']) }}</p>
              @elseif ($product->description)
                {!! $product->description !!}
              @endif
            </div>
          </div>

          <div class="tab-pane fade" id="pane-files" role="tabpanel" aria-labelledby="tab-files" tabindex="0">
            <ul class="km-pd-files row g-3">
              @forelse ($product->catalogDownloads() as $catalog)
                <li class="col-md-6 col-xl-4">
                  <a href="{{ $catalog['url'] }}" download>
                    <span class="km-pd-file-icon"><i class="fa-regular fa-file-pdf"></i></span>
                    <span><strong>{{ $catalog['name'] }}</strong><small>PDF · {{ $catalog['size'] }}</small></span>
                    <i class="fa-solid fa-download"></i>
                  </a>
                </li>
              @empty
                <li class="col-12">
                  <p class="mb-0">{{ __('site.product_no_files') }}</p>
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--
  ##################################################
  #                Related products                #
  ##################################################
-->
  <section class="km-related km-pd-related">
    <div class="container">
      <div class="km-related-head d-flex flex-wrap align-items-end justify-content-between gap-3" data-anim="up">
        <div>
          <div class="km-eyebrow">
            <span class="km-eyebrow-text">{{ __('site.product_related_eyebrow') }}</span>
            <span class="km-eyebrow-line"></span>
          </div>
          <h2>
            @foreach (__('site.product_related_lines') as $line)
              @if ($loop->last)<span class="text-km-gradient">{{ $line }}</span>@else {{ $line }} @endif
            @endforeach
          </h2>
        </div>
        <a href="{{ route('products.index') }}" class="km-btn">{{ __('site.all_products') }} <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span></a>
      </div>

      <div class="row g-3 g-xl-4">
        @foreach ($relatedProducts as $related)
          <div class="col-sm-6 col-lg-3">
            <article class="km-product-card" data-anim="up">
              <div class="km-product-media">
                <img src="{{ $related->mainImageUrl() }}" alt="{{ $related->name }}" loading="lazy" decoding="async" width="600" height="450" />
                <form method="post" action="{{ route('cart.store') }}">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $related->id }}" />
                  <input type="hidden" name="qty" value="1" />
                  <button type="submit" class="km-product-cart" aria-label="{{ __('site.home_add_to_cart') }}"><i class="fa-solid fa-cart-plus"></i></button>
                </form>
              </div>
              <div class="km-product-body">
                <h4 class="km-product-name">{{ $related->name }}</h4>
                @if ($related->short_description)
                  <ul class="km-product-specs">
                    <li><i class="fa-solid fa-circle-info"></i><span>{{ $related->short_description }}</span></li>
                  </ul>
                @endif
                <a href="{{ route('products.show', $related) }}" class="km-btn km-btn-block km-product-btn"><span>{{ __('site.home_view_details') }}</span><span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span></a>
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
