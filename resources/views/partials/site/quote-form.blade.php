@php
    $compact ??= false;
    $formTitle ??= __('site.quote_form_title');
    $selectedServiceId ??= null;
    $fieldPrefix = 'quote'.($compact ? 'Side' : '');
    $columnClass = $compact ? 'col-12' : 'col-md-6';
@endphp

<div class="quote-form-card {{ $compact ? 'mt-4' : 'h-100' }}">
    <h3 class="quote-form-card-title {{ $compact ? 'mb-3' : 'mb-4' }}">{{ $formTitle }}</h3>

    <form action="{{ route('contact.store') }}" method="POST" data-quote-form>
        @csrf
        <x-site.utm-fields />
        <div class="row g-3">
            <div class="{{ $columnClass }}">
                <div class="field-wrap">
                    <label class="form-label-clean" for="{{ $fieldPrefix }}Name">{{ __('site.full_name') }}</label>
                    <input type="text" id="{{ $fieldPrefix }}Name" name="name" autocomplete="name"
                        class="form-control form-input-clean" placeholder="{{ __('site.quote_name_placeholder') }}" required />
                </div>
            </div>

            <div class="{{ $columnClass }}">
                <div class="field-wrap">
                    <label class="form-label-clean" for="{{ $fieldPrefix }}Company">{{ __('site.company') }}</label>
                    <input type="text" id="{{ $fieldPrefix }}Company" name="company" autocomplete="organization"
                        class="form-control form-input-clean" placeholder="{{ __('site.quote_company_placeholder') }}" />
                </div>
            </div>

            <div class="{{ $columnClass }}">
                <div class="field-wrap">
                    <label class="form-label-clean" for="{{ $fieldPrefix }}Email">{{ __('site.email_address') }}</label>
                    <input type="email" id="{{ $fieldPrefix }}Email" name="email" autocomplete="email"
                        class="form-control form-input-clean" placeholder="you@company.com" required />
                </div>
            </div>

            <div class="{{ $columnClass }}">
                <div class="field-wrap">
                    <label class="form-label-clean" for="{{ $fieldPrefix }}Phone">{{ __('site.phone_number') }}</label>
                    <input type="tel" id="{{ $fieldPrefix }}Phone" name="phone" autocomplete="tel"
                        class="form-control form-input-clean" placeholder="+20 2 4489 0141" required />
                </div>
            </div>

            <div class="col-12">
                <div class="field-wrap">
                    <label class="form-label-clean" for="{{ $fieldPrefix }}Packaging">{{ __('site.packaging_type') }}</label>
                    <select id="{{ $fieldPrefix }}Packaging" name="service_id" class="form-select form-input-clean">
                        <option value="" @selected($selectedServiceId === null)>{{ __('site.select_packaging_type') }}</option>
                        @foreach ($mainServices ?? [] as $service)
                            <option value="{{ $service->id }}" @selected($selectedServiceId === $service->id)>{{ $service->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-12">
                <div class="field-wrap">
                    <label class="form-label-clean" for="{{ $fieldPrefix }}Details">{{ __('site.project_details') }}</label>
                    <textarea id="{{ $fieldPrefix }}Details" name="message" class="form-control form-input-clean form-textarea-clean"
                        rows="3" placeholder="{{ __('site.project_details_placeholder') }}"></textarea>
                </div>
            </div>

            <div class="col-12">
                <x-site.turnstile :enabled="$turnstileEnabled ?? false" :site-key="$turnstileSiteKey ?? null" />
            </div>

            <div class="col-12 {{ $compact ? 'mt-2' : 'mt-3' }} pt-1">
                <div class="btn-stacked-wrap {{ $compact ? 'w-100' : '' }}">
                    <button type="submit" class="btn-quote-send {{ $compact ? 'w-100 justify-content-center' : '' }}">
                        <span>{{ __('site.send_your_request') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                @if ($compact)
                    <p class="quote-disclaimer mt-3 mb-0">{{ __('site.quote_disclaimer') }}</p>
                @endif
            </div>
        </div>
    </form>
</div>
