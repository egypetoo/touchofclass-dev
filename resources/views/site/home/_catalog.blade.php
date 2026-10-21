@php
    $homeCatalog = $productMenu
        ->map(function ($category) {
            return [
                'category' => $category,
                'products' => $category->products->concat($category->children->flatMap->products)->values(),
            ];
        })
        ->filter(fn (array $row): bool => $row['products']->isNotEmpty())
        ->values();

    $homeTabColumn = match ($homeCatalog->count()) {
        1 => 'col-12',
        2 => 'col-6',
        3 => 'col-6 col-lg-4',
        default => 'col-6 col-lg',
    };

    $firstCategory = $homeCatalog->first()['category'] ?? null;
@endphp

<div class="container position-relative">
  @if ($homeCatalog->isNotEmpty())
    <div class="km-products-tabs" role="tablist" aria-label="{{ __('site.home_products_eyebrow') }}">
      <div class="row g-2 g-lg-0">
        @foreach ($homeCatalog as $row)
          @php
            $category = $row['category'];
            $slug = $category->getTranslation('slug', 'en');
            $count = $row['products']->count();
            $countLabel = trans_choice('site.home_products_count', $count, ['count' => $count]);
            $icon = match ($slug) {
                'deep-well-motors' => 'fa-solid fa-gears',
                'deep-well-pumps' => 'fa-solid fa-fan',
                'deep-well-pipes' => 'fa-solid fa-grip-lines-vertical',
                'submersible-cables' => 'fa-solid fa-plug-circle-bolt',
                'electrical-panels' => 'fa-solid fa-solar-panel',
                default => 'fa-solid fa-box',
            };
          @endphp
          <div class="{{ $homeTabColumn }}">
            <button type="button" class="km-tab w-100 h-100 {{ $loop->first ? 'active' : '' }}" id="km-tab-{{ $slug }}" role="tab" data-bs-toggle="tab" data-bs-target="#km-pane-{{ $slug }}" aria-controls="km-pane-{{ $slug }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-cat="{{ $slug }}" data-desc="{{ $category->short_text }}">
              <span class="km-tab-icon"><i class="{{ $icon }}"></i></span>
              <span class="km-tab-text"><strong>{{ $category->name }}</strong><small>({{ $countLabel }})</small></span>
            </button>
          </div>
        @endforeach
      </div>
    </div>

    <div class="km-products-toolbar">
      <div class="km-products-heading">
        <h3 id="km-cat-title">{{ $firstCategory->name }}</h3>
        <p id="km-cat-desc">{{ $firstCategory->short_text }}</p>
      </div>

      <div class="km-products-tools">
        <label class="km-search">
          <span class="visually-hidden">{{ __('site.home_search_product') }}</span>
          <input type="search" id="km-product-search" placeholder="{{ __('site.home_search_placeholder') }}" autocomplete="off" />
          <i class="fa-solid fa-magnifying-glass"></i>
        </label>
        <label class="km-sort">
          <span class="visually-hidden">{{ __('site.home_sort_products') }}</span>
          <select id="km-product-sort">
            <option value="default">{{ __('site.home_sort_by') }}</option>
            <option value="name">{{ __('site.home_sort_name') }}</option>
          </select>
        </label>
        <div class="km-view-toggle" role="radiogroup" aria-label="{{ __('site.home_view_mode') }}">
          <input type="radio" class="btn-check" name="km-view" id="km-view-grid" value="grid" autocomplete="off" aria-label="{{ __('site.home_view_grid') }}" checked />
          <label for="km-view-grid" title="{{ __('site.home_view_grid') }}"><i class="fa-solid fa-table-cells-large"></i></label>
          <input type="radio" class="btn-check" name="km-view" id="km-view-list" value="list" autocomplete="off" aria-label="{{ __('site.home_view_list') }}" />
          <label for="km-view-list" title="{{ __('site.home_view_list') }}"><i class="fa-solid fa-list"></i></label>
        </div>
      </div>
    </div>

    <div class="tab-content km-products-panes">
      @foreach ($homeCatalog as $row)
        @php
          $category = $row['category'];
          $slug = $category->getTranslation('slug', 'en');
        @endphp
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="km-pane-{{ $slug }}" role="tabpanel" aria-labelledby="km-tab-{{ $slug }}" tabindex="0">
          <div class="km-products-stage">
            <div class="swiper km-products-swiper" aria-label="{{ $category->name }}">
              <div class="swiper-wrapper">
                @foreach ($row['products'] as $product)
                  @php
                    $image = $product->mainImageUrl() !== '' ? $product->mainImageUrl() : asset('assets/images/products/pump-submersible.webp');
                    $brand = $product->specText(($product->specifications ?? [])['brand'] ?? '');
                  @endphp
                  <div class="swiper-slide" data-value="{{ $product->sort_order }}">
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
                          @if ($brand !== '')
                            <li><i class="fa-solid fa-tag"></i><span>{{ $brand }}</span></li>
                          @endif
                          @if ($product->short_description)
                            <li><i class="fa-solid fa-circle-info"></i><span>{{ $product->short_description }}</span></li>
                          @endif
                        </ul>
                        <a href="{{ route('products.show', $product) }}" class="km-btn km-btn-block km-product-btn"><span>{{ __('site.home_view_details') }}</span><span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span></a>
                      </div>
                    </article>
                  </div>
                @endforeach
              </div>
            </div>
            <p class="km-products-empty" hidden>{{ __('site.home_no_search_results') }}</p>
          </div>

          <div class="km-products-nav">
            <button type="button" class="km-products-arrow km-products-prev" aria-label="{{ __('site.home_previous') }}"><i class="fa-solid fa-arrow-right"></i></button>
            <div class="km-products-dots"></div>
            <button type="button" class="km-products-arrow km-products-next" aria-label="{{ __('site.home_next') }}"><i class="fa-solid fa-arrow-left"></i></button>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
