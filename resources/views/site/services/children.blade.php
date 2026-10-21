@extends('layouts.site')

@section('content')
    <x-site.breadcrumb
        :image="$services_breadcrumb?->imageUrl('sliders/slider3.jpg')"
        :title="$service->title"
        :subtitle="$service->short_text ? strip_tags($service->short_text) : null"
        :eyebrow="$service->label ?: __('site.our_solutions')"
        :parent-title="__('site.solutions')"
        :parent-url="route('services.index')"
    />

    @include('site.services._grid')
@endsection
