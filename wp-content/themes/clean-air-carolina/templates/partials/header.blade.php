<header class="main">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-6 col-sm-2">
        <a class="brand" href="{{ home_url('/') }}">
          @include('partials.logo')
        </a>
      </div>
      <div class="col-xs-6 col-md-10">
        <nav class="nav-primary">
          @if (has_nav_menu('primary_navigation'))
            {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
          @endif
        </nav>
        <div class="search-action-wrap">
          <button id="search-action">
            <i class="fa fa-search"></i>
          </button>
        </div>
        <div class="donate-wrap">
          @php
            echo do_shortcode("[donate_button]");
          @endphp
        </div>
      </div>
    </div>
  </div>
</header>
