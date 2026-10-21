@extends('layouts.site')

@section('content')
    <x-site.breadcrumb
        :image="$termsAndConditions?->imageUrl('sliders/slider3.jpg')"
        :title="$termsAndConditions?->title ?: __('site.terms_and_conditions')"
        :subtitle="$termsAndConditions?->sub_title"
    />

    @include('site.pages._legal-content', ['legalSection' => $termsAndConditions])
@endsection
