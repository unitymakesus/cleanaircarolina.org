<footer class="content-info main">
  @php(dynamic_sidebar('sidebar-footer'))
  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          @include('partials.logo')
          <div class="contact-info">
            <address>
              PO Box 5311 Charlotte, NC 28299
            </address>
            <a href="tel:7043079528">(704) 307-9528</a>
          </div>
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
            @php
              echo do_shortcode("[donate_button]");
            @endphp
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-middle">
    <div class="container">
      <div class="footer-logos-wrap">
        @include('partials.footer-logos')
      </div>
      <div class="search-form-wrap">
        @php(get_search_form())
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <nav class="nav-footer">
      @if (has_nav_menu('micro_navigation'))
        {!! wp_nav_menu(['theme_location' => 'micro_navigation', 'menu_class' => 'nav']) !!}
      @endif
    </nav>
  </div>
</footer>
