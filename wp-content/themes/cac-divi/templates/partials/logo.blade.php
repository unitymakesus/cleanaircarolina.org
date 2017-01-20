@php
  use App as A;
  $logo = @file_get_contents(A\sage('assets')->getUri('images/svg/clean-air-carolina.svg'));
@endphp

<div class="logo-wrap">
  @if ($logo !== FALSE)
    {!! $logo !!}
  @endif
</div>
