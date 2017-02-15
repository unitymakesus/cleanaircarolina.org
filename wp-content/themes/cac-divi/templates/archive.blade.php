@extends('layouts.base')

@section('content')
  @if (!have_posts())
    <div class="alert alert-warning">
      {{ __('Sorry, no results were found.', 'sage') }}
    </div>
    {!! get_search_form(false) !!}
  @endif

  {!! do_shortcode('[et_pb_section global_module="11695"]') !!}

  {!! get_the_posts_navigation() !!}
@endsection
