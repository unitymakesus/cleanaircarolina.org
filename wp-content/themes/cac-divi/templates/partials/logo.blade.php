@php
  use App as A;
  $logo = @file_get_contents(A\sage('assets')->getUri('images/svg/clean-air-carolina.svg'));
@endphp

<div class="logo-wrap">
  <a class="brand" href="{{ esc_url( home_url( '/' ) ) }}">
    @if ($logo !== FALSE)
      {!! $logo !!}
    @endif
  </a>
</div>
