<header class="main">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-5 col-sm-5 col-md-3 col-lg-2 logo-col">
        @include('partials.logo')
      </div>
      <div class="col-xs-7 col-sm-7 col-md-9 col-lg-10 nav-col">
        <button class="navbar-toggler hidden-lg-up btn btn-primary"
                type="button"
                data-toggle="collapse"
                data-target="#exCollapsingNavbar"
                aria-controls="exCollapsingNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">
          <i class="fa fa-bars"></i>
        </button>
        <div class="collapse nav-primary-wrap navbar-toggleable-md" id="exCollapsingNavbar">
          <button class="navbar-toggler hidden-lg-up btn btn-primary"
          type="button"
          data-toggle="collapse"
          data-target="#exCollapsingNavbar"
          aria-controls="exCollapsingNavbar"
          aria-expanded="false"
          aria-label="Toggle navigation">
            <i class="fa fa-times"></i>
          </button>
          <nav class="nav-primary">
            @if (has_nav_menu('primary_navigation'))
              {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
            @endif
          </nav>
        </div>
        <div class="donate-wrap">
          @php
          echo do_shortcode("[donate_button]");
          @endphp
        </div>
        <div class="search-action-wrap">
          <button id="search-action">
            <i class="fa fa-search"></i>
          </button>

          <div id="search-popover" class="search-popover">
            {!! get_search_form() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
