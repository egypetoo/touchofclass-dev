@extends('layouts.site')

@section('content')
    <x-site.breadcrumb
        :image="$projects_breadcrumb?->imageUrl('sliders/slider1.jpg')"
        :title="$projects_breadcrumb?->title ?: __('site.industries')"
        :subtitle="$projects_breadcrumb?->text ? strip_tags($projects_breadcrumb->text) : null"
        :eyebrow="$projects_breadcrumb?->sub_title ?: __('site.our_markets')"
        :crumb-title="__('site.industries')"
    />

    <section class="industries-section py-5">
        <div class="container py-4">
            <div class="row g-4">
                @forelse ($projects as $project)
                    @php($isWideRow = $loop->index >= 3)
                    <div class="{{ $isWideRow ? 'col-md-6 col-lg-6' : 'col-md-6 col-lg-4' }}" data-aos="fade-up"
                        data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                        @include('site.projects._card', ['project' => $project, 'index' => $loop->index])
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">{{ __('site.no_items_yet') }}</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
