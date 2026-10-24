@extends('layouts.site')

@section('content')
 
  @include('site.home._hero')

  @include('site.home._about')

  @include('site.home._services')

  @include('site.home._projects')

  {{-- @include('site.home._brands') --}}

  @include('site.home._why_us')

  @include('site.home._blogs')

  @include('site.home._contact')

@endsection
