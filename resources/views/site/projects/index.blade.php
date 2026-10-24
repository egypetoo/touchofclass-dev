@extends('layouts.site')

@section('content')
    <section class="km-page-header">
        <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-1.webp') }}" width="1920" height="1080" alt="" aria-hidden="true" fetchpriority="high" />
        <div class="container position-relative text-center">
            <h1 class="km-page-title" data-anim="up">{{ $projects_breadcrumb?->title ?: __('site.projects') }}</h1>
            <nav aria-label="breadcrumb" data-anim="up">
                <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $projects_breadcrumb?->title ?: __('site.projects') }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="km-products is-static-grid" id="products">
        <div class="container position-relative">
            <div class="km-products-tabs" role="tablist" aria-label="{{ __('site.projects_title') }}">
                <div class="row g-2 g-lg-0">
                    @foreach ($projectCategories as $category)
                        <div class="{{ $projectTabColumn }}">
                            <button type="button" class="km-tab w-100 h-100 {{ ($activeProjectCategory['slug'] ?? '') === $category['slug'] ? 'active' : '' }}" id="km-tab-{{ $category['slug'] }}" role="tab" data-bs-toggle="tab" data-bs-target="#km-pane-{{ $category['slug'] }}" aria-controls="km-pane-{{ $category['slug'] }}" aria-selected="{{ ($activeProjectCategory['slug'] ?? '') === $category['slug'] ? 'true' : 'false' }}" data-cat="{{ $category['slug'] }}" data-desc="{{ $category['description'] }}">
                                <span class="km-tab-icon"><i class="{{ $category['icon'] }}"></i></span>
                                <span class="km-tab-text"><strong>{{ $category['name'] }}</strong>@if ($category['description'] !== '')<small>({{ $category['description'] }})</small>@endif</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="km-products-toolbar">
                <div class="km-products-heading">
                    <h3 id="km-cat-title">{{ $activeProjectCategory['name'] ?? __('site.projects_title') }}</h3>
                    <p id="km-cat-desc">{{ $activeProjectCategory['description'] ?? '' }}</p>
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
                @forelse ($projectCategories as $category)
                    <div class="tab-pane fade {{ ($activeProjectCategory['slug'] ?? '') === $category['slug'] ? 'show active' : '' }}" id="km-pane-{{ $category['slug'] }}" role="tabpanel" aria-labelledby="km-tab-{{ $category['slug'] }}" tabindex="0">
                        <div class="km-products-stage">
                            <div class="km-projects-grid" aria-label="{{ $category['name'] }}">
                                @foreach ($category['projects'] as $project)
                                    <article class="km-product-card km-album" data-value="{{ $project['value'] }}">
                                        <div class="km-product-media km-album-photos">
                                            @foreach ($project['images'] as $image)
                                                <a href="{{ $image }}" data-index="{{ $loop->index }}"><img src="{{ $image }}" alt="{{ $project['name'] }}" loading="lazy" decoding="async"></a>
                                            @endforeach
                                        </div>
                                        <div class="km-product-body">
                                            <h4 class="km-product-name km-album-name">{{ $project['name'] }}</h4>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <p class="km-products-empty" hidden>{{ __('site.no_projects') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-5">{{ __('site.no_projects') }}</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
