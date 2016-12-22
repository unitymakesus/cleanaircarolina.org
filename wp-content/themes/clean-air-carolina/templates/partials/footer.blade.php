<footer class="content-info main">
  <div class="container">
    @php(dynamic_sidebar('sidebar-footer'))
    <div class="footer-top">
      <div class="row">
        <div class="col-md-3">
          LOGO
        </div>
        <div class="col-md-3">
          <nav class="nav-footer">
            @if (has_nav_menu('footer_navigation'))
              {!! wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'nav']) !!}
            @endif
          </nav>
        </div>
        <div class="col-md-3">
          <div class="nav-social-wrap">
            <h4>Follow Us</h4>
            <nav class="nav-social">
              @if (has_nav_menu('social_navigation'))
                {!! wp_nav_menu(['theme_location' => 'social_navigation', 'menu_class' => 'nav']) !!}
              @endif
            </nav>
          </div>
        </div>
        <div class="col-md-3">
          <div class="donate-wrap">
            <h4>Help Make A Difference Today</h4>
            DONATE SHORTCODE
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      LOGO LOGO LOGO SEARCH
      @php get_search_form(); @endphp
    </div>
  </div>
</footer>
