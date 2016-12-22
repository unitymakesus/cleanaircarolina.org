{{--
  Template Name: Home Page Template
--}}

@extends('layouts.base')

@section('content')
    @while(have_posts()) @php(the_post())
    @include('partials.content-page')
    @include('partials.content-home')
    @endwhile
@endsection
