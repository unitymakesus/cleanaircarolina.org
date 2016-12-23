<div class="logo-wrap">
@php
    $theme_url      = get_template_directory();
    $image_asset    = $theme_url . '/assets/images/svg/';
    $logo_path      = $image_asset . 'clean-air-carolina.svg';
    $svg            = file_get_contents( $logo_path );

    echo $svg;
@endphp
</div>