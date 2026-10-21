@php
    $showSectionHeader ??= true;
@endphp

<section class="quote-section py-5" id="contact"
    style="background-image: url('{{ Path::imagesPath('banner/bg.png') }}'); background-repeat: no-repeat; background-position: center; background-size: cover;">
    <div id="quote" class="position-absolute" style="top: -80px;"></div>
    <div class="container py-lg-4 py-3 position-relative z-1">
        @if ($showSectionHeader)
            <div class="quote-header mb-4 mb-lg-5" data-aos="fade-up">
                <span class="badge-contact-pill">{{ $contact_form_section?->sub_title ?: __('site.contact_us') }}</span>
                @if ($contact_form_section?->title)
                    <h2 class="quote-main-title mt-3 mb-2">{{ $contact_form_section->titleHtml() }}</h2>
                @endif
                @if ($contact_form_section?->text)
                    <p class="quote-main-subtitle mb-0">{{ strip_tags($contact_form_section->text) }}</p>
                @endif
            </div>
        @endif

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="talk-team-card h-100">
                    <div class="talk-team-content">
                        <h3 class="talk-team-title">{{ $contact_content?->title ?: __('site.talk_to_our_team') }}</h3>
                        @if ($contact_content?->text)
                            <p class="talk-team-desc">{{ strip_tags($contact_content->text) }}</p>
                        @endif

                        @include('partials.site.contact-list')
                    </div>

                    <div class="talk-team-visual">
                        <img src="{{ $contact_content?->imageUrl('banner/cardboard-layers@2x.png') ?? Path::imagesPath('banner/cardboard-layers@2x.png') }}"
                            alt="{{ $contact_content?->alt_image ?: $siteDisplayName }}" class="img-fluid" loading="lazy" />
                    </div>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                @include('partials.site.quote-form', ['formTitle' => $contact_form_section?->button_text ?: __('site.quote_form_title')])
            </div>
        </div>
    </div>
</section>
