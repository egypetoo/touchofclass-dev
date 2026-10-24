 <!--
      ##################################################
      #                    Articles                    #
      ##################################################
    -->
    <section class="km-articles" id="articles">


        <div class="container position-relative">
          <div class="km-articles-head">
            <div class="km-articles-intro">
              <div class="km-eyebrow justify-content-center">
                <span class="km-eyebrow-line"></span>
                <span class="km-eyebrow-text">{{ $homeBlogsIntro?->sub_title ?: __('site.home_articles_eyebrow') }}</span>
                <span class="km-eyebrow-line"></span>
              </div>
              <h2 class="km-articles-title"><span class="text-km-gradient">{{ $homeBlogsIntro?->titleLines()[0] ?? __('site.home_articles_lines.0') }}</span> {{ filled($homeBlogsIntro?->second_title) ? $homeBlogsIntro->second_title : ($homeBlogsIntro?->titleLines()[1] ?? '') }}</h2>
              <p class="km-articles-desc">{!! filled($homeBlogsIntro?->text) ? $homeBlogsIntro->text : e(__('site.home_articles_text')) !!}</p>
            </div>
    
          </div>
    
          <div class="km-articles-grid row g-3">
            @if ($homeFeaturedArticle)
            <div class="col-12 col-xl-4 order-xl-last">
              <article class="km-article h-100 is-featured">
                <img class="km-article-bg" src="{{ $homeFeaturedArticle['image'] }}" sizes="(max-width: 991px) 92vw, 520px" width="1024" height="576" alt="{{ $homeFeaturedArticle['alt'] }}" loading="lazy" decoding="async"/>
                <span class="km-article-badge">{{ __('site.home_featured_article') }}</span>
                <div class="km-article-body">
                  <time class="km-article-date" datetime="{{ $homeFeaturedArticle['datetime'] }}"><i class="fa-regular fa-calendar-days"></i> {{ $homeFeaturedArticle['date'] }}</time>
                  <h3 class="km-article-title"><a href="{{ $homeFeaturedArticle['url'] }}">{{ $homeFeaturedArticle['title'] }}</a></h3>
                  <p class="km-article-excerpt">{{ $homeFeaturedArticle['excerpt'] }}</p>
                  <a href="{{ $homeFeaturedArticle['url'] }}" class="km-article-more" aria-label="{{ __('site.read_more') }}: {{ $homeFeaturedArticle['title'] }}">
                    <span class="km-article-more-icon"><i class="fa-solid fa-arrow-left"></i></span>
                    <span>{{ __('site.read_more') }}</span>
                  </a>
                </div>
              </article>
            </div>
            @endif
            @foreach ($homeArticles as $article)
            <div class="col-md-4 col-xl">
              <article class="km-article h-100">
                <a href="{{ $article['url'] }}" class="km-article-media" tabindex="-1" aria-hidden="true">
                  <img src="{{ $article['image'] }}" width="640" height="480" alt="{{ $article['alt'] }}" loading="lazy" decoding="async"/>
                </a>
                <div class="km-article-body">
                  <time class="km-article-date" datetime="{{ $article['datetime'] }}"><i class="fa-regular fa-calendar-days"></i> {{ $article['date'] }}</time>
                  <h3 class="km-article-title"><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
                  <p class="km-article-excerpt">{{ $article['excerpt'] }}</p>
                  <a href="{{ $article['url'] }}" class="km-article-more" aria-label="{{ __('site.read_more') }}: {{ $article['title'] }}">
                    <span class="km-article-more-icon"><i class="fa-solid fa-arrow-left"></i></span>
                    <span>{{ __('site.read_more') }}</span>
                  </a>
                </div>
              </article>
            </div>
            @endforeach
          </div>
          <div class="d-flex justify-content-center mt-5" data-anim="up">
            <a href="{{ $homeBlogsIntro?->button_link_url ?: route('news.index') }}" class="km-btn km-articles-all">
              <span>{{ $homeBlogsIntro?->button_text ?: __('site.home_articles_button') }}</span>
              <span class="km-btn-icon"><i class="fa-solid fa-arrow-left"></i></span>
            </a>
          </div>
        </div>
      </section>