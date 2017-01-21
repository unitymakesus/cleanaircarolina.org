{{--
  Template Name: No Navigation Bar
--}}

@extends('layouts.base-no-nav')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.content-page')
  @endwhile
@endsection
