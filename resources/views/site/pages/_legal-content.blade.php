<section class="inner-section py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="details-content" data-aos="fade-up">
                    @if (filled($legalSection?->text))
                        {!! $legalSection->text !!}
                    @else
                        <p class="mb-0">{{ __('site.page_content_coming_soon') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
