@extends('layouts.site')

@section('content')
  @php
    $whatsapp = $siteSetting?->ctaWhatsappEntries()[0] ?? null;
  @endphp
  <!--
    ##################################################
    #                  Contact hero                  #
    ##################################################
  -->
  <section class="km-chero">
    <img
      class="km-page-header-bg"
      src="{{ asset('assets/images/hero/hero-1.webp') }}"
      width="1920"
      height="1080"
      alt=""
      aria-hidden="true"
      fetchpriority="high"
    />
    <div class="container position-relative">
      <div class="row g-5 align-items-start">
        <div class="col-lg-6">
          <div class="km-chero-content">
            <nav aria-label="breadcrumb" data-anim="up">
              <ol class="breadcrumb km-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('site.contact_us') }}</li>
              </ol>
            </nav>
            <h1 class="km-chero-title" data-anim="up">{{ $contact_content?->title ?: __('site.contact_us') }}</h1>
            @php
              $heroAddresses = collect($branches ?? [])
                  ->map(fn ($place): array => [
                      'name' => trim(strip_tags((string) $place->name)),
                      'address' => trim(strip_tags((string) $place->address)),
                  ])
                  ->filter(fn (array $place): bool => $place['address'] !== '')
                  ->values();
              $heroHours = trim(strip_tags((string) ($mainBranch?->working_hours ?? '')));
              $badgeIcons = ['fa-solid fa-bolt', 'fa-solid fa-truck-fast', 'fa-solid fa-award'];
              $badgeFallbacks = [
                  __('site.contact_reply_fast'),
                  __('site.contact_nationwide'),
                  __('site.contact_experience'),
              ];
            @endphp
            @if ($contactPhones !== [] || $heroAddresses->isNotEmpty() || $heroHours !== '')
              <ul class="km-chero-facts" data-anim="up">
                @foreach ($contactPhones as $phone)
                  <li>
                    <i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
                    <a href="tel:{{ $phone['tel'] }}" dir="ltr">{{ $phone['display'] }}</a>
                  </li>
                @endforeach
                @foreach ($heroAddresses as $place)
                  <li>
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <span>
                      @if ($heroAddresses->count() > 1 && $place['name'] !== '')
                        <strong>{{ $place['name'] }}</strong>
                      @endif
                      {{ $place['address'] }}
                    </span>
                  </li>
                @endforeach
                @if ($heroHours !== '')
                  <li>
                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                    <span>{{ $heroHours }}</span>
                  </li>
                @endif
              </ul>
            @else
              <p class="km-chero-desc" data-anim="up">{{ $contact_content?->text ? trim(strip_tags($contact_content->text)) : __('site.contact_form_desc') }}</p>
            @endif
            <div class="km-chero-actions" data-anim="up">
              @if ($primaryPhone)
                <a class="km-chero-btn is-gold" href="tel:{{ $primaryPhone['tel'] }}">
                  <i class="fa-solid fa-phone-volume"></i>
                  <span>{{ __('site.contact_call_now') }}</span>
                </a>
              @endif
              @if ($whatsappUrl)
                <a class="km-chero-btn is-outline" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
                  <i class="fa-brands fa-whatsapp"></i>
                  <span>{{ __('site.whatsapp') }}</span>
                </a>
              @endif
            </div>
            <ul class="km-chero-badges" data-anim="up">
              @forelse ($contactBadges as $badge)
                <li><i class="{{ $badgeIcons[$loop->index] ?? 'fa-solid fa-check' }}"></i> {{ $badge->title }}</li>
              @empty
                @foreach ($badgeFallbacks as $index => $badge)
                  <li><i class="{{ $badgeIcons[$index] }}"></i> {{ $badge }}</li>
                @endforeach
              @endforelse
            </ul>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="km-cform-card" id="contact" data-anim="up">
            @if ($contact_form_section?->title)
              <h2 class="km-cform-card-title">{{ $contact_form_section->title }}</h2>
            @else
              <h2 class="km-cform-card-title">{{ __('site.home_contact_form_lead') }} <span class="text-km-gradient">{{ __('site.home_contact_form_rest') }}</span></h2>
            @endif
            <p class="km-cform-card-desc">{{ $contact_form_section?->text ? strip_tags($contact_form_section->text) : __('site.contact_form_desc') }}</p>

            <form class="row g-3 needs-validation" id="contact-form" action="{{ route('contact.store') }}" method="post" novalidate>
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
                <label for="contact-phone" class="km-label">{{ __('site.home_phone') }} <span>*</span></label>
                <div class="km-contact-field">
                  <input type="tel" id="contact-phone" name="phone" value="{{ old('phone') }}" class="km-input" placeholder="{{ __('site.home_phone_placeholder') }}" autocomplete="tel" required/>
                  <i class="fa-solid fa-phone"></i>
                </div>
              </div>
              <div class="col-12">
                <label for="contact-type" class="km-label">{{ __('site.home_request_type') }}</label>
                <select id="contact-type" name="service_id" class="km-input km-select">
                  <option value="">{{ __('site.home_choose_request') }}</option>
                  @foreach ($contactServices as $service)
                    <option value="{{ $service->id }}" @selected((string) old('service_id') === (string) $service->id)>{{ $service->title }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12">
                <label for="contact-email" class="km-label">{{ __('site.email') }}</label>
                <div class="km-contact-field">
                  <input type="email" id="contact-email" name="email" value="{{ old('email') }}" class="km-input" placeholder="{{ __('site.home_email_placeholder') }}" autocomplete="email"/>
                  <i class="fa-regular fa-envelope"></i>
                </div>
              </div>
              <div class="col-12">
                <label for="contact-message" class="km-label">{{ __('site.home_message') }}</label>
                <div class="km-contact-field is-textarea">
                  <textarea id="contact-message" name="message" class="km-input" rows="4" placeholder="{{ __('site.contact_message_placeholder') }}">{{ old('message') }}</textarea>
                  <i class="fa-regular fa-message"></i>
                </div>
              </div>
              <x-site.turnstile :enabled="$turnstileEnabled" :site-key="$turnstileSiteKey"/>
              <div class="col-12">
                <button type="submit" class="km-btn km-btn-block">
                  <span>{{ __('site.home_send_message') }}</span>
                  <span class="km-btn-icon"><i class="fa-regular fa-paper-plane"></i></span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--
    ##################################################
    #                    Location                    #
    ##################################################
  -->
  <section class="km-location">
    <div class="container">
      <div class="km-location-box" data-anim="up">
        <div class="row g-0">
          <div class="col-lg-7">
            <div class="km-location-map">
              @if ($mainBranch)
                {!! $mainBranch->mapEmbedHtml() !!}
              @endif
            </div>
          </div>
          <div class="col-lg-5">
            <div class="km-location-info">
              <div class="km-eyebrow">
                <span class="km-eyebrow-text">{{ __('site.visit_us') }}</span>
                <span class="km-eyebrow-line"></span>
              </div>
              @php
                $listedBranches = collect($branches ?? [])->filter(
                    fn ($place): bool => trim(strip_tags((string) $place->address)) !== ''
                )->values();
              @endphp
              @if ($listedBranches->count() > 1)
                <h2>{{ __('site.visit_us') }}</h2>
                @foreach ($listedBranches as $place)
                  <p class="km-location-address">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>
                      @if (filled($place->name))
                        <strong>{{ $place->name }}</strong>
                      @endif
                      {{ trim(strip_tags((string) $place->address)) }}
                    </span>
                  </p>
                @endforeach
              @else
                <h2>{{ $mainBranch?->name ?: __('site.contact_headquarters') }}</h2>
                @if ($contactAddress)
                  <p class="km-location-address"><i class="fa-solid fa-location-dot"></i> {{ $contactAddress }}</p>
                @endif
              @endif

              @if ($mainBranch?->working_hours)
                <h3 class="km-location-sub">{{ __('site.working_hours') }}</h3>
                <ul class="km-hours">
                  <li><span>{{ trim(strip_tags($mainBranch->working_hours)) }}</span></li>
                </ul>
              @endif

              <div class="km-location-actions">
                @if ($mainBranch?->map_url)
                  <a class="km-btn" href="{{ $mainBranch->map_url }}" target="_blank" rel="noopener">
                    <span>{{ __('site.contact_directions') }}</span>
                    <span class="km-btn-icon"><i class="fa-solid fa-diamond-turn-right"></i></span>
                  </a>
                @endif
                <div class="km-location-socials">
                  @foreach ($siteSetting?->socialLinks ?? [] as $link)
                    @if (filled($link->url) && $link->url !== '#')
                      <a href="{{ $link->url }}" target="_blank" rel="noopener" aria-label="{{ $link->platformEnum()?->getLabel() }}">
                        <i class="{{ $link->platformEnum()?->iconClass() ?? 'fa-solid fa-globe' }}"></i>
                      </a>
                    @endif
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--
    ##################################################
    #                    Channels                    #
    ##################################################
  -->
  <section class="km-channels">
    <img class="km-channels-bg" src="{{ asset('assets/images/hero/hero-2.webp') }}" width="1920" height="1080" alt="" aria-hidden="true" loading="lazy" decoding="async"/>
    <div class="container position-relative">
      <div class="km-channels-head" data-anim="up">
        <div class="km-eyebrow justify-content-center">
          <span class="km-eyebrow-line"></span>
          <span class="km-eyebrow-text">{{ $contactChannelsIntro?->sub_title ?: __('site.contact_channels') }}</span>
          <span class="km-eyebrow-line"></span>
        </div>
        <h2>{{ $contactChannelsIntro?->title ?: __('site.contact_channels_title') }}</h2>
        <p>{{ $contactChannelsIntro?->text ? trim(strip_tags($contactChannelsIntro->text)) : __('site.contact_channels_text') }}</p>
      </div>

      <div class="km-channels-grid row gx-0 gy-4 gy-md-0" data-anim="up">
        @php
          $whatsappCard = $contactChannelCards->get(1);
          $phoneCard = $contactChannelCards->get(2);
          $emailCard = $contactChannelCards->get(3);
        @endphp
        @if ($whatsappUrl)
          <a class="km-channel col-md-4" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
            <span class="km-channel-icon"><i class="fa-brands fa-whatsapp"></i></span>
            <h3>{{ $whatsappCard?->title ?: __('site.whatsapp') }}</h3>
            <p>{{ $whatsappCard?->text ? trim(strip_tags($whatsappCard->text)) : __('site.contact_whatsapp_desc') }}</p>
            <strong class="km-channel-value" dir="ltr">{{ $whatsapp['display'] ?? '' }}</strong>
            <span class="km-channel-cta">{{ $whatsappCard?->button_text ?: __('site.contact_start_chat') }} <i class="fa-solid fa-arrow-left"></i></span>
          </a>
        @endif
        @if ($primaryPhone)
          <a class="km-channel col-md-4" href="tel:{{ $primaryPhone['tel'] }}">
            <span class="km-channel-icon"><i class="fa-solid fa-phone-volume"></i></span>
            <h3>{{ $phoneCard?->title ?: __('site.contact_phone_direct') }}</h3>
            <p>{{ $phoneCard?->text ? trim(strip_tags($phoneCard->text)) : __('site.contact_phone_desc') }}</p>
            <strong class="km-channel-value" dir="ltr">{{ $primaryPhone['display'] }}</strong>
            <span class="km-channel-cta">{{ $phoneCard?->button_text ?: __('site.contact_call_now') }} <i class="fa-solid fa-arrow-left"></i></span>
          </a>
        @endif
        @if ($contactEmail)
          <a class="km-channel col-md-4" href="mailto:{{ $contactEmail }}">
            <span class="km-channel-icon"><i class="fa-regular fa-envelope"></i></span>
            <h3>{{ $emailCard?->title ?: __('site.email') }}</h3>
            <p>{{ $emailCard?->text ? trim(strip_tags($emailCard->text)) : __('site.contact_email_desc') }}</p>
            <strong class="km-channel-value">{{ $contactEmail }}</strong>
            <span class="km-channel-cta">{{ $emailCard?->button_text ?: __('site.contact_send_email') }} <i class="fa-solid fa-arrow-left"></i></span>
          </a>
        @endif
      </div>
    </div>
  </section>



  <!--
    ##################################################
    #                  Footer                        #
    ##################################################
  -->
@endsection
