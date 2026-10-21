@extends('layouts.site')

@section('content')
    <x-site.breadcrumb
        :image="$privacyPolicy?->imageUrl('sliders/slider2.jpg')"
        :title="$privacyPolicy?->title ?: __('site.privacy_policy')"
        :subtitle="$privacyPolicy?->sub_title"
    />

    @include('site.pages._legal-content', ['legalSection' => $privacyPolicy])
@endsection
