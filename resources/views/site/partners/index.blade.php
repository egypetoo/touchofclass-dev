@extends('layouts.site')

@section('content')
    <x-site.breadcrumb
        :image="$clients_breadcrumb?->imageUrl('hero/hero-slide-1@2x.png')"
        :title="$clients_breadcrumb?->title ?: __('site.our_clients')"
        :subtitle="$clients_breadcrumb?->text ? strip_tags($clients_breadcrumb->text) : null"
        :eyebrow="$clients_breadcrumb?->sub_title ?: __('site.our_clients')"
        :crumb-title="__('site.clients')"
    />

    <section class="clients-page-section bg-light-alt py-5">
        <div class="container py-4">
            @if ($sectorGroups->isNotEmpty())
                <div class="clients-cat-nav-wrap" data-aos="fade-up">
                    <nav class="clients-cat-nav" aria-label="{{ __('site.client_categories') }}">
                        @foreach ($sectorGroups as $group)
                            <a href="#{{ $group['sector']->value }}" class="clients-cat-link">
                                <i class="{{ $group['sector']->iconClass() }}"></i>
                                <span>{{ $group['sector']->getLabel() }}</span>
                                <small>{{ $group['partners']->count() }}</small>
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif

            @foreach ($sectorGroups as $group)
                @include('site.partners._category', [
                    'categoryId' => $group['sector']->value,
                    'categoryIndex' => $loop->iteration,
                    'categoryTitle' => $group['sector']->getLabel(),
                    'categoryPartners' => $group['partners'],
                ])
            @endforeach

            @if ($unassignedPartners->isNotEmpty())
                @include('site.partners._category', [
                    'categoryId' => 'other-clients',
                    'categoryIndex' => $sectorGroups->count() + 1,
                    'categoryTitle' => __('site.other_clients'),
                    'categoryPartners' => $unassignedPartners,
                ])
            @endif

            @if ($sectorGroups->isEmpty() && $unassignedPartners->isEmpty())
                <p class="text-center text-muted py-5 mb-0">{{ __('site.no_items_yet') }}</p>
            @endif
        </div>
    </section>
@endsection
