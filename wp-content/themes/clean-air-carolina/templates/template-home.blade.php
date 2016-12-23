{{--
  Template Name: Home Page Template
--}}

@extends('layouts.base')

@section('content')
    @while(have_posts()) @php(the_post())
    @include('partials.content-hero', [
        'type'  => 'video',

    ])
    <div class="container">
        @include('partials.content-page')
        @php
            $background = get_template_directory_uri() . '/assets/images/background-counter.jpg';
        @endphp
    </div>
    <div class="event-box clearfix" style="background-image:url({{$background}});">
        <div class="container">
            <h2 class="date">March <span>16</span></h2>
            <div class="content">
                <h3>NC Breath Conference</h3>
                <h4>Bridging research on economics and air quality for the health of everyone.</h4>
                <a class="btn btn-primary btn-lg" href="#register">Register Now</a>
            </div>
        </div>
    </div>
    <div class="container">
        @include('partials.content-home')
    </div>
    @endwhile
@endsection
