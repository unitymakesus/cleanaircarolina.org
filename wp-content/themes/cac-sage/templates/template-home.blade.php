{{--
  Template Name: Home Page Template
--}}

@extends('layouts.base')

@section('content')
    @while(have_posts()) @php(the_post())
    {{--@include('partials.content-hero', [--}}
        {{--'type'  => 'video',--}}

    {{--])--}}
    @php(the_content())
    {{--<div class="container">--}}
      {{--<div class="row">--}}
        {{--<div class="col-md-4">--}}
          {{--<div class="icon-message first">--}}
            {{--<figure class="circle-icon">--}}
              {{--{!! do_shortcode('[inline_svg name="circle-clouds"]') !!}--}}
            {{--</figure>--}}
            {{--<h4>Most air pollution in NC is invisible and invisible air pollution can be the most deadly.</h4>--}}
          {{--</div>--}}
        {{--</div>--}}
        {{--<div class="col-md-4">--}}
          {{--<div class="icon-message">--}}
            {{--<figure class="circle-icon">--}}
              {{--{!! do_shortcode('[inline_svg name="circle-nc"]') !!}--}}
            {{--</figure>--}}
            {{--<h4>North Carolina is one of the fastest growing states in the country.</h4>--}}
          {{--</div>--}}
        {{--</div>--}}
        {{--<div class="col-md-4">--}}
          {{--<div class="icon-message">--}}
            {{--<figure class="circle-icon">--}}
              {{--{!! do_shortcode('[inline_svg name="circle-gavel"]') !!}--}}
            {{--</figure>--}}
            {{--<h4>The anti-regulatory climate in the NC legislature has rolled back strong clean air policies.</h4>--}}
          {{--</div>--}}
        {{--</div>--}}
      {{--</div>--}}

      {{--<div class="row">--}}
        {{--<div class="col-md-8 offset-md-2">--}}
          {{--<div class="call-to-action-box">--}}
            {{--<h3 class="title">Act Now</h3>--}}
            {{--<h5>Sign up for free newsletter and action alerts</h5>--}}
            {{--<div class="newsletter-signup">--}}
              {{--<!-- Begin MailChimp Signup Form -->--}}
              {{--<div id="mc_embed_signup" class="mailchimp-signup">--}}
                  {{--<form action="//acodesmith.us13.list-manage.com/subscribe/post?u=f771a5bf552cd475fc41b7d14&amp;id=XXXXXXX" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>--}}
                      {{--<div id="mc_embed_signup_scroll">--}}
                          {{--<div class="mc-field-group">--}}
                              {{--<input type="email" value="" placeholder="Email Address" name="EMAIL" class="required email" id="mce-EMAIL"><input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="button">--}}
                          {{--</div>--}}
                          {{--<div style="position: absolute; left: -5000px;" aria-hidden="true">--}}
                              {{--<input type="text" name="b_f771a5bf552cd475fc41b7d14_c193ce9772" tabindex="-1" value="">--}}
                          {{--</div>--}}
                      {{--</div>--}}
                  {{--</form>--}}
              {{--</div>--}}
              {{--<!--End mc_embed_signup-->--}}
            {{--</div>--}}
          {{--</div>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
    {{--@php--}}
        {{--$background = get_template_directory_uri() . '/assets/images/background-counter.jpg';--}}
    {{--@endphp--}}
    {{--<div class="event-box clearfix" style="background-image:url({{$background}});">--}}
        {{--<div class="container">--}}
            {{--<h2 class="date">March <span>16</span></h2>--}}
            {{--<div class="content">--}}
                {{--<h3>NC BREATHE Conference</h3>--}}
                {{--<h4>Bridging research on economics and air quality for the health of everyone.</h4>--}}
                {{--<a class="btn btn-primary btn-lg" href="#register">Register Now</a>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="container">--}}
        {{--@include('partials.content-home')--}}
    {{--</div>--}}
    @endwhile
@endsection
