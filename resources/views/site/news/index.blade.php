@extends('layouts.site')

@section('content')
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-3.webp') }}" width="1920" height="1080" alt="" aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title" data-anim="up">{{ __('site.news_and_articles') }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('site.news') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <section class="km-news">
    <div class="container">
      @php
        $featured = $blogs->currentPage() === 1 ? $blogs->first() : null;
      @endphp

      @if ($featured)
        @php
          $featuredImage = $featured->getFirstMediaUrl('blogs') ?: asset('assets/images/articles/article-featured.webp');
          $featuredUrl = route('news.show', $featured);
        @endphp
        <article class="km-news-featured row g-0" data-anim="up">
          <a href="{{ $featuredUrl }}" class="km-news-featured-media col-lg-6" tabindex="-1" aria-hidden="true">
            <img src="{{ $featuredImage }}" width="1024" height="576" alt="{{ $featured->title }}" loading="lazy" decoding="async" />
            <span class="km-article-badge">{{ __('site.home_featured_article') }}</span>
          </a>
          <div class="km-news-featured-body col-lg-6">
            @if ($featured->published_at)
              <div class="km-news-meta">
                <time datetime="{{ $featured->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days"></i> {{ $featured->published_at->translatedFormat('j F Y') }}</time>
              </div>
            @endif
            <h2><a href="{{ $featuredUrl }}">{{ $featured->title }}</a></h2>
            @if ($featured->excerpt)
              <p>{{ strip_tags($featured->excerpt) }}</p>
            @endif
            <a href="{{ $featuredUrl }}" class="km-btn"><span>{{ __('site.read_more') }}</span><span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span></a>
          </div>
        </article>
      @endif

      <div class="row g-4">
        @foreach ($blogs as $article)
          @continue($featured && $article->is($featured))
          @php
            $articleImage = $article->getFirstMediaUrl('blogs') ?: asset('assets/images/articles/article-featured.webp');
            $articleUrl = route('news.show', $article);
          @endphp
          <div class="col-md-6 col-lg-4">
            <article class="km-article h-100">
              <a href="{{ $articleUrl }}" class="km-article-media" tabindex="-1" aria-hidden="true">
                <img src="{{ $articleImage }}" width="640" height="480" alt="{{ $article->title }}" loading="lazy" decoding="async" />
              </a>
              <div class="km-article-body">
                @if ($article->published_at)
                  <div class="km-news-meta">
                    <time datetime="{{ $article->published_at->toDateString() }}"><i class="fa-regular fa-calendar-days"></i> {{ $article->published_at->translatedFormat('j F Y') }}</time>
                  </div>
                @endif
                <h3 class="km-article-title"><a href="{{ $articleUrl }}">{{ $article->title }}</a></h3>
                @if ($article->excerpt)
                  <p class="km-article-excerpt">{{ strip_tags($article->excerpt) }}</p>
                @endif
                <a href="{{ $articleUrl }}" class="km-article-more" aria-label="{{ __('site.home_read_more_about', ['title' => $article->title]) }}">
                  <span class="km-article-more-icon"><i class="fa-solid fa-arrow-left"></i></span>
                  <span>{{ __('site.read_more') }}</span>
                </a>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      @if ($blogs->isEmpty())
        <p>{{ __('site.no_items_yet') }}</p>
      @endif

      @if ($blogs->hasPages())
        <nav class="km-shop-pagination" aria-label="{{ __('site.news') }}">
          {{ $blogs->links() }}
        </nav>
      @endif
    </div>
  </section>
@endsection
