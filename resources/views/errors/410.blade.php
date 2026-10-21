@extends('layouts.site')

@section('content')
    <section class="py-5" style="min-height: 60vh; display: flex; align-items: center;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="display-1 fw-bold text-danger">410</div>
                    <h1 class="mt-3">{{ __('site.page_gone_title') }}</h1>
                    <p class="text-muted mb-4">{{ __('site.page_gone_message') }}</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('site.home') }}</a>
                        <a href="{{ route('contact-us') }}" class="btn btn-outline-secondary">{{ __('site.contact_us') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
