<section class="km-contact" id="contact">
    <div class="km-contact-bg" aria-hidden="true"></div>

    <div class="container position-relative">
        <div class="km-contact-grid">
            <div class="row">
                <div class="col-lg-4 me-lg-auto km-contact-intro">
                    <div class="km-contact-head">
                        <div class="km-eyebrow">
                            <span class="km-eyebrow-text">{{ $homeContact['eyebrow'] }}</span>
                            <span class="km-eyebrow-line"></span>
                        </div>
                        <h2 class="km-contact-title"><span class="text-km-gradient">{{ $homeContact['line'] }}</span> {{ $homeContact['second'] }}</h2>
                        <p class="km-contact-desc">{{ $homeContact['text'] }}</p>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-0">
                <div class="col-lg-3 order-lg-3 km-contact-aside">
                    <div class="km-contact-info">
                        <ul class="km-contact-cards row g-3">
                            @foreach ($homeContact['cards'] as $card)
                                <li class="col-12 col-sm-6 col-lg-12">
                                    <div class="km-contact-item h-100">
                                        <span class="km-contact-icon"><i class="{{ $card['icon'] }}"></i></span>
                                        <div>
                                            <h3>{{ $card['title'] }}</h3>
                                            @if ($card['href'])
                                                <a href="{{ $card['href'] }}" @if ($card['ltr']) dir="ltr" @endif>{{ $card['text'] }}</a>
                                            @elseif ($card['lines'] !== [])
                                                <p>
                                                    @foreach ($card['lines'] as $line)
                                                        {{ $line }}@if (! $loop->last)<br/>@endif
                                                    @endforeach
                                                </p>
                                            @else
                                                <p>{{ $card['text'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="km-contact-socials">
                            @foreach ($homeContact['socials'] as $social)
                                <a href="{{ $social['url'] }}" aria-label="{{ $social['label'] }}" @if ($social['external']) target="_blank" rel="noopener" @endif>
                                    <i class="{{ $social['icon'] }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 order-lg-2">
                    <div class="km-contact-card">
                        <h3 class="km-contact-card-title">
                            <span class="km-contact-card-line"></span>
                            <span><span class="text-km-gradient">{{ $homeContact['form']['lead'] }}</span> {{ $homeContact['form']['rest'] }}</span>
                        </h3>
                        <p class="km-contact-card-desc">{{ $homeContact['form']['description'] }}</p>

                        <form class="row g-3 needs-validation" id="contact-form" action="{{ $homeContact['form']['action'] }}" method="post" novalidate>
                            @csrf
                            <x-site.utm-fields/>
                            <div class="col-md-6">
                                <label for="contact-name" class="km-label">{{ __('site.home_full_name') }} <span>*</span></label>
                                <div class="km-contact-field">
                                    <input type="text" id="contact-name" name="name" value="{{ old('name') }}" class="km-input" placeholder="{{ __('site.home_name_placeholder') }}" autocomplete="name" required/>
                                    <i class="fa-regular fa-user"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-email" class="km-label">{{ __('site.email') }} <span>*</span></label>
                                <div class="km-contact-field">
                                    <input type="email" id="contact-email" name="email" value="{{ old('email') }}" class="km-input" placeholder="{{ __('site.home_email_placeholder') }}" autocomplete="email" required/>
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-phone" class="km-label">{{ __('site.home_phone') }} <span>*</span></label>
                                <div class="km-contact-field">
                                    <input type="tel" id="contact-phone" name="phone" value="{{ old('phone') }}" class="km-input" placeholder="{{ __('site.home_phone_placeholder') }}" autocomplete="tel" required/>
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-type" class="km-label">{{ __('site.home_request_type') }}</label>
                                <select id="contact-type" name="service_id" class="km-input km-select">
                                    <option value="" @selected(blank(old('service_id'))) disabled>{{ __('site.home_choose_request') }}</option>
                                    @foreach ($homeContact['form']['requests'] as $requestType)
                                        <option value="{{ $requestType['id'] }}" @selected((string) old('service_id') === (string) $requestType['id'])>{{ $requestType['title'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="contact-message" class="km-label">{{ __('site.home_message') }} <span>*</span></label>
                                <div class="km-contact-field is-textarea">
                                    <textarea id="contact-message" name="message" class="km-input" rows="3" placeholder="{{ __('site.home_message_placeholder') }}" required>{{ old('message') }}</textarea>
                                    <i class="fa-regular fa-message"></i>
                                </div>
                            </div>
                            <x-site.turnstile :enabled="$turnstileEnabled" :site-key="$turnstileSiteKey"/>
                            <div class="col-12">
                                <button type="submit" class="km-btn km-btn-block km-contact-submit">
                                    <span>{{ __('site.home_send_message') }}</span>
                                    <span class="km-btn-icon"><i class="fa-regular fa-paper-plane"></i></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 order-lg-1 d-flex km-contact-map-col">
                    <div class="km-contact-map w-100">
                        {!! $homeContact['mapHtml'] !!}
                        @if ($homeContact['mapUrl'] !== '')
                            <a class="km-contact-map-link" href="{{ $homeContact['mapUrl'] }}" target="_blank" rel="noopener">
                                <span>{{ __('site.home_map_link') }}</span>
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
