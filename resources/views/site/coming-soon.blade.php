<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteSetting?->comingSoonTitle() ?? __('site.coming_soon_default_title') }} | {{ $siteSetting?->siteName() ?? config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ $siteSetting?->faviconUrl() ?? asset('assets/images/logo/logo.png') }}" type="image/png" />
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <style>
        .coming-soon-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: #fff;
            text-align: center;
            background: #0b0f0c;
        }

        .coming-soon-page .coming-soon-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.45;
        }

        .coming-soon-page .coming-soon-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(8, 20, 13, 0.82) 0%, rgba(4, 11, 7, 0.94) 100%);
        }

        .coming-soon-page .coming-soon-card {
            position: relative;
            z-index: 2;
            max-width: 720px;
            padding: 2.5rem 1.5rem;
        }

        .coming-soon-page .coming-soon-logo {
            width: 120px;
            height: auto;
            margin-inline: auto 0;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.35));
        }

        .coming-soon-page h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .coming-soon-page .coming-soon-message {
            font-size: 1.05rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 2rem;
        }

        .coming-soon-page .coming-soon-contacts {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .coming-soon-page .coming-soon-contacts a {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.85rem 1.25rem;
            border-radius: 999px;
            background: rgba(140, 198, 63, 0.16);
            border: 1px solid rgba(140, 198, 63, 0.4);
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: 0.25s ease;
        }

        .coming-soon-page .coming-soon-contacts a:hover {
            background: #8cc63f;
            color: #0b0f0c;
        }
    </style>
</head>

<body>
    <section class="coming-soon-page">
        <img src="{{ asset('assets/images/cta/cta-field-bg.jpg') }}" alt="" class="coming-soon-bg" aria-hidden="true" />
        <div class="coming-soon-overlay" aria-hidden="true"></div>

        <div class="coming-soon-card">
            <img src="{{ $siteSetting?->logoUrl() ?? asset('assets/images/logo/logo.png') }}"
                alt="{{ $siteSetting?->siteName() ?? 'King Motors' }}" class="coming-soon-logo" />

            <h1>{{ $siteSetting?->comingSoonTitle() ?? __('site.coming_soon_default_title') }}</h1>
            <p class="coming-soon-message mb-0">
                {{ $siteSetting?->comingSoonMessage() ?? __('site.coming_soon_default_message') }}
            </p>

            <div class="coming-soon-contacts mt-4">
                @if ($siteSetting?->primaryCtaPhoneTel())
                    <a href="tel:{{ $siteSetting->primaryCtaPhoneTel() }}" dir="ltr">
                        <i class="fas fa-phone"></i>
                        <span>{{ $siteSetting->primaryCtaPhoneTel() }}</span>
                    </a>
                @endif

                @if ($siteSetting?->whatsappUrl())
                    <a href="{{ $siteSetting->whatsappUrl() }}" target="_blank" rel="noopener" dir="ltr">
                        <i class="fab fa-whatsapp"></i>
                        <span>{{ __('site.whatsapp') }}</span>
                    </a>
                @endif

                @if ($siteSetting?->support_email)
                    <a href="mailto:{{ $siteSetting->support_email }}" dir="ltr">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $siteSetting->support_email }}</span>
                    </a>
                @endif
            </div>
        </div>
    </section>
</body>

</html>
