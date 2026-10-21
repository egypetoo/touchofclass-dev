@extends('layouts.site')

@section('content')
    <x-site.breadcrumb
        :image="$bannerImage"
        :title="$project->title"
        :subtitle="$project->short_text ?: null"
        :eyebrow="__('site.industry_details')"
        :parent-title="__('site.industries')"
        :parent-url="route('projects.index')"
    />

    <section class="inner-section py-5">
        <div class="container py-4">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-8">
                    @include('partials.site.details-gallery', ['galleryName' => 'industry-gallery', 'galleryCaption' => $project->title])

                    <div class="details-content mt-4" data-aos="fade-up">
                        <span class="badge-eyebrow-pill mb-3">{{ __('site.industry_number', ['number' => $industryNumber]) }}</span>
                        {!! $project->text !!}
                    </div>

                    @if ($project->featureList() !== [])
                        <div class="details-content mt-4" data-aos="fade-up">
                            <h3>{{ __('site.recommended_solutions') }}</h3>
                            <ul class="feature-list">
                                @foreach ($project->featureList() as $feature)
                                    <li><i class="fas fa-check"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    @include('partials.site.details-sidebar', [
                        'sidebarTitle' => __('site.industries_we_serve'),
                        'sidebarLinks' => $industries->map(fn ($industry) => [
                            'title' => $industry->title,
                            'url' => route('projects.show', $industry),
                            'active' => $industry->is($project),
                        ]),
                    ])
                </div>
            </div>
        </div>
    </section>
@endsection
