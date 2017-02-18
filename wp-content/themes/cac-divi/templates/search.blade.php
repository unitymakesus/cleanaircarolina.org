@extends('layouts.base')

<div class="container">
  @section('content')

    {!! do_shortcode('[et_pb_section global_module="11758"]') !!}

    {!! get_the_posts_navigation() !!}
  @endsection
</div>
