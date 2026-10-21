@php
    $phone = $siteSetting?->ctaPhoneEntries()[0] ?? null;
    $whatsapp = $siteSetting?->ctaWhatsappEntries()[0] ?? null;
@endphp

@if ($phone || ($whatsapp && ($whatsapp['url'] ?? null)))
    <div class="content-call-buttons" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="content-call-buttons__list">
            @if ($phone)
                <div class="content-call-buttons__item content-call-buttons__item--phone">
                    <a class="content-call-buttons__link" href="tel:{{ $phone['tel'] }}" rel="noopener" dir="ltr">
                        <i class="ph ph-phone-call" aria-hidden="true"></i>
                        <span>{{ $siteSetting->ctaLabel() ?: $phone['display'] }}</span>
                    </a>
                </div>
            @endif

            @if ($whatsapp && ($whatsapp['url'] ?? null))
                <div class="content-call-buttons__item content-call-buttons__item--whatsapp">
                    <a class="content-call-buttons__link" href="{{ $whatsapp['url'] }}" target="_blank"
                        rel="noopener noreferrer" dir="ltr">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        <span>{{ __('site.whatsapp') }}</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        .content-call-buttons {
            margin: 1.5rem 0;
        }

        .content-call-buttons__list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: center;
            gap: 1rem;
        }

        .content-call-buttons__item {
            padding: 10px 20px;
            background-color: #2a3f66;
            border-radius: 20px;
        }

        .content-call-buttons__item--whatsapp {
            background-color: #25d366;
        }

        .content-call-buttons__link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #fff !important;
            text-decoration: none !important;
            font-weight: 600;
        }

        .content-call-buttons__link:hover {
            opacity: 0.9;
            color: #fff !important;
        }
    </style>
@endif
