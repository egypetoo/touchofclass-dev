 <!--
      ##################################################
      #                   Projects                     #
      ##################################################
    -->
    <section class="km-products" id="products">
        <div class="km-products-banner">
          <img src="{{ $homeProjectsIntro?->imageUrl('studio/urban.jpg') }}" sizes="(max-width: 991px) 185vw, 66vw" alt="{{ $homeProjectsIntro?->alt_image ?: ($homeProjectsIntro?->title ?? __('site.projects_title')) }}" class="km-products-banner-img" width="1600" height="900" loading="lazy" decoding="async"/>
          <div class="km-products-banner-shade" aria-hidden="true"></div>
          <svg class="km-products-banner-lines" viewBox="0 0 1000 340" preserveAspectRatio="none" aria-hidden="true">
            <path d="M516 0 L430 340"/>
            <path d="M552 0 L466 340"/>
            <path d="M0 236 L110 340"/>
          </svg>
    
          <div class="container position-relative">
            <div class="km-products-intro">
              <div class="km-eyebrow">
                <span class="km-eyebrow-text">{{ $homeProjectsIntro?->sub_title ?: __('site.projects_title') }}</span>
                <span class="km-eyebrow-line"></span>
              </div>
              <h2 class="km-products-title"><span class="text-km-gradient">{{ $homeProjectsIntro?->titleLines()[0] ?? __('site.projects_title') }}</span> {{ filled($homeProjectsIntro?->second_title) ? $homeProjectsIntro->second_title : ($homeProjectsIntro?->titleLines()[1] ?? '') }}</h2>
              <p>{!! filled($homeProjectsIntro?->text) ? $homeProjectsIntro->text : __('site.projects_description') !!}</p>
              <a href="{{ $homeProjectsIntro?->button_link_url ?: route('projects.index') }}" class="km-btn mt-3">
                <span>{{ $homeProjectsIntro?->button_text ?: __('site.view_all_projects') }}</span>
                <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
              </a>
            </div>
    
    
    
          </div>
        </div>
    
        <div class="container position-relative">
          <div class="km-products-tabs" role="tablist" aria-label="{{ __('site.projects_title') }}">
            <div class="row g-2 g-lg-0">
              @foreach ($homeProjectCategories as $category)
                <div class="{{ $homeProjectTabColumn }}">
                  <button type="button" class="km-tab w-100 h-100 {{ $loop->first ? 'active' : '' }}" id="km-tab-{{ $category['slug'] }}" role="tab" data-bs-toggle="tab" data-bs-target="#km-pane-{{ $category['slug'] }}" aria-controls="km-pane-{{ $category['slug'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-cat="{{ $category['slug'] }}" data-desc="{{ $category['description'] }}">
                    <span class="km-tab-icon"><i class="{{ $category['icon'] }}"></i></span>
                    <span class="km-tab-text"><strong>{{ $category['name'] }}</strong>@if ($category['description'] !== '')<small>({{ $category['description'] }})</small>@endif</span>
                  </button>
                </div>
              @endforeach
            </div>
          </div>
          <div class="km-products-toolbar">
            <div class="km-products-heading">
              <h3 id="km-cat-title">{{ $homeProjectCategories->value('name') ?? __('site.projects_title') }}</h3>
              <p id="km-cat-desc">{{ $homeProjectCategories->value('description') ?? __('site.projects_description') }}</p>
            </div>
            <div class="km-products-tools">
              <label class="km-search">
                <span class="visually-hidden">{{ __('site.search_for_project') }}</span>
                <input type="search" id="km-product-search" placeholder="{{ __('site.search_for_project') }}..." autocomplete="off"/>
                <i class="fa-solid fa-magnifying-glass"></i>
              </label>
              <label class="km-sort">
                <span class="visually-hidden">{{ __('site.sort_projects') }}</span>
                <select id="km-product-sort">
                  <option value="default">{{ __('site.sort_by') }}</option>
                  <option value="name">{{ __('site.name') }}</option>
                  <option value="value-asc">{{ __('site.area_small') }}</option>
                  <option value="value-desc">{{ __('site.area_large') }}</option>
                </select>
              </label>
              <div class="km-view-toggle" role="radiogroup" aria-label="{{ __('site.view_method') }}">
                <input type="radio" class="btn-check" name="km-view" id="km-view-grid" value="grid" autocomplete="off" aria-label="{{ __('site.grid_view') }}" checked/>
                <label for="km-view-grid" title="{{ __('site.grid_view') }}"><i class="fa-solid fa-table-cells-large"></i></label>
                <input type="radio" class="btn-check" name="km-view" id="km-view-list" value="list" autocomplete="off" aria-label="{{ __('site.list_view') }}"/>
                <label for="km-view-list" title="{{ __('site.list_view') }}"><i class="fa-solid fa-list"></i></label>
              </div>
            </div>
          </div>
    
          <div class="tab-content km-products-panes">
            @foreach ($homeProjectCategories as $category)
              <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="km-pane-{{ $category['slug'] }}" role="tabpanel" aria-labelledby="km-tab-{{ $category['slug'] }}" tabindex="0">
                <div class="km-products-stage">
                  <div class="swiper km-products-swiper" aria-label="{{ $category['name'] }}">
                    <div class="swiper-wrapper">
                      @foreach ($category['projects'] as $project)
                        <div class="swiper-slide" data-value="{{ $project['value'] }}">
                          <article class="km-product-card km-album">
                            <div class="km-product-media km-album-photos">
                              @foreach ($project['images'] as $image)
                                <a href="{{ $image }}" data-index="{{ $loop->index }}"><img src="{{ $image }}" alt="{{ $project['name'] }}" loading="lazy" decoding="async"></a>
                              @endforeach
                            </div>
                            <div class="km-product-body">
                              <h4 class="km-product-name km-album-name">{{ $project['name'] }}</h4>
                            </div>
                          </article>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <p class="km-products-empty" hidden>{{ __('site.no_projects') }}</p>
                </div>
    
                <div class="km-products-nav">
                  <button type="button" class="km-products-arrow km-products-prev" aria-label="{{ __('site.home_previous') }}"><i class="fa-solid fa-arrow-right"></i></button>
                  <div class="km-products-dots"></div>
                  <button type="button" class="km-products-arrow km-products-next" aria-label="{{ __('site.home_next') }}"><i class="fa-solid fa-arrow-left"></i></button>
                </div>
              </div>
            @endforeach
          </div>
    
        </div>
      </section>