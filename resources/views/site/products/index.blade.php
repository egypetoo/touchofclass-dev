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
      <h1 class="km-page-title" data-anim="up">{{ $products_breadcrumb?->title ?: __('site.our_products') }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $products_breadcrumb?->title ?: __('site.our_products') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!--
  ##################################################
  #                   Products                     #
  ##################################################
-->
  <section class="km-shop" id="products">
    <div class="container">
      <div class="row g-4 g-xl-5">

        <!-- Filters -->
        <aside class="col-lg-3">
          <div class="offcanvas-lg offcanvas-end km-shop-filters" id="km-shop-filters" tabindex="-1" aria-labelledby="km-shop-filters-title">
            <div class="offcanvas-header">
              <h2 class="km-shop-filters-title" id="km-shop-filters-title"><i class="fa-solid fa-sliders"></i> {{ __('site.shop_filters') }}</h2>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#km-shop-filters" aria-label="{{ __('site.close') }}"></button>
            </div>
            <div class="offcanvas-body">
              <form class="km-shop-form" id="km-shop-form" action="{{ route('products.index') }}" method="get">

                <div class="km-filter">
                  <h3 class="km-filter-title">{{ __('site.search') }}</h3>
                  <label class="km-search w-100">
                    <span class="visually-hidden">{{ __('site.home_search_product') }}</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('site.shop_search_placeholder') }}" autocomplete="off" />
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </label>
                </div>

                <div class="km-filter">
                  <h3 class="km-filter-title">{{ __('site.categories') }}</h3>
                  <ul class="km-filter-list">
                    <li>
                      <input type="radio" class="km-filter-input" name="category" id="cat-all" value="" @checked(blank($activeCategorySlug)) />
                      <label for="cat-all"><span>{{ __('site.all_products') }}</span><small>{{ $allProductsCount }}</small></label>
                    </li>
                    @foreach ($filterCategories as $category)
                      <li>
                        <input type="radio" class="km-filter-input" name="category" id="cat-{{ $category->getTranslation('slug', 'en') }}" value="{{ $category->getTranslation('slug', 'en') }}" @checked($activeCategorySlug === $category->getTranslation('slug', 'en')) />
                        <label for="cat-{{ $category->getTranslation('slug', 'en') }}"><span>{{ $category->name }}</span><small>{{ $category->products_count }}</small></label>
                      </li>
                    @endforeach
                  </ul>
                </div>

                <div class="km-filter">
                  <h3 class="km-filter-title">{{ __('site.shop_brands') }}</h3>
                  <ul class="km-filter-list">
                    @foreach ($brands as $brand)
                      <li>
                        <input type="checkbox" class="km-filter-input" name="brand[]" id="brand-{{ \Illuminate\Support\Str::slug($brand['value']) }}" value="{{ $brand['value'] }}" @checked(in_array($brand['value'], (array) request('brand', []), true)) />
                        <label for="brand-{{ \Illuminate\Support\Str::slug($brand['value']) }}"><span>{{ $brand['label'] }}</span><small>{{ $brand['count'] }}</small></label>
                      </li>
                    @endforeach
                  </ul>
                </div>

                <div class="km-filter">
                  <h3 class="km-filter-title">{{ __('site.shop_power') }}</h3>
                  <div class="km-filter-range">
                    <label>
                      <span class="visually-hidden">{{ __('site.shop_from') }}</span>
                      <input type="number" class="km-input" name="power_min" value="{{ request('power_min') }}" placeholder="{{ __('site.shop_from') }}" min="0" step="any" inputmode="decimal" />
                    </label>
                    <span class="km-filter-range-sep">—</span>
                    <label>
                      <span class="visually-hidden">{{ __('site.shop_to') }}</span>
                      <input type="number" class="km-input" name="power_max" value="{{ request('power_max') }}" placeholder="{{ __('site.shop_to') }}" min="0" step="any" inputmode="decimal" />
                    </label>
                  </div>
                </div>

                <div class="km-filter-actions">
                  <button type="submit" class="km-btn km-btn-block">
                    <span>{{ __('site.shop_apply') }}</span>
                    <span class="km-btn-icon"><i class="fa-solid fa-filter"></i></span>
                  </button>
                  <a href="{{ route('products.index') }}" class="km-filter-reset">{{ __('site.shop_clear') }}</a>
                </div>

              </form>
            </div>
          </div>
        </aside>

        <!-- Results -->
        <div class="col-lg-9">
          <div class="km-shop-toolbar">
            <div class="km-shop-count">
              <button type="button" class="km-shop-filter-btn d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#km-shop-filters" aria-controls="km-shop-filters">
                <i class="fa-solid fa-sliders"></i> {{ __('site.shop_filters') }}
              </button>
              <p>
                @if ($products->total())
                  {!! __('site.shop_showing', [
                      'from' => '<strong>'.e((string) $products->firstItem()).'</strong>',
                      'to' => '<strong>'.e((string) $products->lastItem()).'</strong>',
                      'total' => '<strong>'.e((string) $products->total()).'</strong>',
                  ]) !!}
                @else
                  {{ __('site.shop_no_matches') }}
                @endif
              </p>
            </div>

            <div class="km-products-tools">
              <label class="km-sort">
                <span class="visually-hidden">{{ __('site.home_sort_products') }}</span>
                <select name="sort" form="km-shop-form" data-autosubmit>
                  <option value="" @selected(request('sort', '') === '')>{{ __('site.shop_newest') }}</option>
                  <option value="name" @selected(request('sort') === 'name' || request('sort') === 'name_asc')>{{ __('site.home_sort_name') }}</option>
                  <option value="power-asc" @selected(request('sort') === 'power-asc')>{{ __('site.shop_power_asc') }}</option>
                  <option value="power-desc" @selected(request('sort') === 'power-desc')>{{ __('site.shop_power_desc') }}</option>
                </select>
              </label>
              <div class="km-view-toggle" role="radiogroup" aria-label="{{ __('site.home_view_mode') }}">
                <input type="radio" class="btn-check" name="km-shop-view" id="km-shop-view-grid" value="grid" autocomplete="off" aria-label="{{ __('site.home_view_grid') }}" checked />
                <label for="km-shop-view-grid" title="{{ __('site.home_view_grid') }}"><i class="fa-solid fa-table-cells-large"></i></label>
                <input type="radio" class="btn-check" name="km-shop-view" id="km-shop-view-list" value="list" autocomplete="off" aria-label="{{ __('site.home_view_list') }}" />
                <label for="km-shop-view-list" title="{{ __('site.home_view_list') }}"><i class="fa-solid fa-list"></i></label>
              </div>
            </div>
          </div>

          <div class="row g-3 g-xl-4 km-shop-grid">
            @forelse ($products as $product)
              @php
                $image = $product->mainImageUrl() !== '' ? $product->mainImageUrl() : asset('assets/images/products/pump-submersible.webp');
              @endphp
              <div class="col-sm-6 col-xl-4">
                <article class="km-product-card">
                  <div class="km-product-media">
                    <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="600" height="450" />
                    <form method="post" action="{{ route('cart.store') }}">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $product->id }}" />
                      <input type="hidden" name="qty" value="1" />
                      <button type="submit" class="km-product-cart" aria-label="{{ __('site.home_add_to_cart') }}"><i class="fa-solid fa-cart-plus"></i></button>
                    </form>
                  </div>
                  <div class="km-product-body">
                    <h4 class="km-product-name">{{ $product->name }}</h4>
                    <ul class="km-product-specs">
                      <li><i class="fa-solid fa-barcode"></i><span>{{ $product->code }}</span></li>
                      @if ($product->category)
                        <li><i class="fa-solid fa-layer-group"></i><span>{{ $product->category->name }}</span></li>
                      @endif
                      @if ($product->short_description)
                        <li><i class="fa-solid fa-circle-info"></i><span>{{ $product->short_description }}</span></li>
                      @endif
                    </ul>
                    <a href="{{ route('products.show', $product) }}" class="km-btn km-btn-block km-product-btn"><span>{{ __('site.home_view_details') }}</span><span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span></a>
                  </div>
                </article>
              </div>
            @empty
              <div class="col-12">
                <p class="text-center mb-0">{{ __('site.shop_no_filter_matches') }}</p>
              </div>
            @endforelse
          </div>

          @if ($products->hasPages())
            <nav class="km-shop-pagination" aria-label="{{ __('site.shop_pages') }}">
              <ul class="pagination">
                @php
                  $previousIcon = app()->getLocale() === 'ar' ? 'fa-angle-right' : 'fa-angle-left';
                  $nextIcon = app()->getLocale() === 'ar' ? 'fa-angle-left' : 'fa-angle-right';
                @endphp
                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                  @if ($products->onFirstPage())
                    <span class="page-link" aria-label="{{ __('site.previous') }}"><i class="fa-solid {{ $previousIcon }}"></i></span>
                  @else
                    <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev" aria-label="{{ __('site.previous') }}"><i class="fa-solid {{ $previousIcon }}"></i></a>
                  @endif
                </li>
                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                  <li class="page-item {{ $page === $products->currentPage() ? 'active' : '' }}" @if ($page === $products->currentPage()) aria-current="page" @endif>
                    @if ($page === $products->currentPage())
                      <span class="page-link">{{ $page }}</span>
                    @else
                      <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                  </li>
                @endforeach
                <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                  @if ($products->hasMorePages())
                    <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next" aria-label="{{ __('site.next') }}"><i class="fa-solid {{ $nextIcon }}"></i></a>
                  @else
                    <span class="page-link" aria-label="{{ __('site.next') }}"><i class="fa-solid {{ $nextIcon }}"></i></span>
                  @endif
                </li>
              </ul>
            </nav>
          @endif
        </div>

      </div>
    </div>
  </section>

  <!--
  ##################################################
  #                  Footer                        #
  ##################################################
-->
@endsection
