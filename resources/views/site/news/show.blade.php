@extends('layouts.site')

@section('content')
<!--
  ##################################################
  #                   Page Header                  #
  ##################################################
-->
  <section class="km-page-header">
    <img class="km-page-header-bg" src="{{ asset('assets/images/hero/hero-3.webp') }}" width="1920" height="1080" alt=""
      aria-hidden="true" fetchpriority="high" />
    <div class="container position-relative text-center">
      <h1 class="km-page-title is-post" data-anim="up">{{ $blog->title }}</h1>
      <nav aria-label="breadcrumb" data-anim="up">
        <ol class="breadcrumb km-breadcrumb justify-content-center mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
          <li class="breadcrumb-item"><a href="{{ route('news.index') }}">{{ __('site.news') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $blog->title }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <!--
  ##################################################
  #                     Article                    #
  ##################################################
-->
  <section class="km-post">
    <div class="container">
      <div class="row g-4 g-xl-5">
        <div class="col-lg-8">
          <article class="km-post-content">
            <div class="km-post-media" data-anim="up">
              <figure class="km-post-figure">
                <img src="{{ $blog->getFirstMediaUrl('blogs') ?: asset('assets/images/articles/article-featured.webp') }}" width="1024" height="576" alt="{{ $blog->title }}" decoding="async" />
              </figure>
              @if ($blog->published_at)
                <time class="km-post-date" datetime="{{ $blog->published_at->toDateString() }}"><strong>{{ $blog->published_at->translatedFormat('j') }}</strong><span>{{ $blog->published_at->translatedFormat('F') }}</span></time>
              @endif
            </div>

            <ul class="km-post-meta">
              @if ($blog->published_at)
                <li><i class="fa-regular fa-calendar-days"></i> <time datetime="{{ $blog->published_at->toDateString() }}">{{ $blog->published_at->translatedFormat('j F Y') }}</time></li>
              @endif
            </ul>

            <div class="km-prose">
              @if ($blog->excerpt)
                <p class="km-prose-lead">{{ strip_tags($blog->excerpt) }}</p>
              @endif
              {!! $blog->content !!}
            </div>

           
          </article>
        </div>

        <aside class="col-lg-4">
          <div class="km-post-sidebar">
            

            <div class="km-widget">
              <h2 class="km-filter-title">{{ __('site.news_latest') }}</h2>
              <ul class="km-widget-posts">
                @foreach ($latestPosts as $post)
                  <li>
                    <a href="{{ route('news.show', $post) }}">
                      <img src="{{ $post->getFirstMediaUrl('blogs') ?: asset('assets/images/articles/article-featured.webp') }}" width="640" height="480" alt="" loading="lazy" decoding="async" />
                      <span>
                        <strong>{{ $post->title }}</strong>
                        @if ($post->published_at)
                          <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('j F Y') }}</time>
                        @endif
                      </span>
                    </a>
                  </li>
                @endforeach
              </ul>
            </div>

            <div class="km-sd-help">
              <img class="km-sd-help-bg" src="{{ asset('assets/images/services/service-consulting-480.webp') }}" width="480" height="640" alt="" aria-hidden="true" loading="lazy" decoding="async" />
              
              <h2>{{ __('site.news_consult_title') }}</h2>
              <p>{{ __('site.news_consult_text') }}</p>
              <form class="km-sd-help-form" action="{{ route('contact.store') }}" method="post">
                @csrf
                <x-site.turnstile :enabled="$turnstileEnabled" :site-key="$turnstileSiteKey" />
                <div>
                  <label for="post-name" class="km-label">{{ __('site.home_full_name') }} <span>*</span></label>
                  <div class="km-contact-field">
                    <input type="text" id="post-name" name="name" class="km-input" placeholder="{{ __('site.home_name_placeholder') }}" autocomplete="name" required />
                    <i class="fa-regular fa-user"></i>
                  </div>
                </div>
                <div>
                  <label for="post-phone" class="km-label">{{ __('site.home_phone') }} <span>*</span></label>
                  <div class="km-contact-field">
                    <input type="tel" id="post-phone" name="phone" class="km-input" placeholder="{{ __('site.home_phone_placeholder') }}" autocomplete="tel" required />
                    <i class="fa-solid fa-phone"></i>
                  </div>
                </div>
                <div>
                  <label for="post-message" class="km-label">{{ __('site.news_project_details') }}</label>
                  <div class="km-contact-field is-textarea">
                    <textarea id="post-message" name="message" class="km-input" rows="3" placeholder="{{ __('site.home_message_placeholder') }}"></textarea>
                    <i class="fa-regular fa-message"></i>
                  </div>
                </div>
                <button type="submit" class="km-btn km-btn-block">
                  <span>{{ __('site.news_request_consult') }}</span>
                  <span class="km-btn-icon"><i class="fa-regular fa-paper-plane"></i></span>
                </button>
              </form>
            
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

 
  <!--
  ##################################################
  #                  Footer                        #
  ##################################################
-->
@endsection
