<!doctype html>
<html @php(language_attributes())>
  @include('partials.head')
  <body @php(body_class())>
    <!--[if IE]>
      <div class="alert alert-warning">
        {!! __('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage') !!}
      </div>
    <![endif]-->
    <a class="screen-reader-text" href="#main">Skip to main content</a>
    @php(do_action('get_header'))
    <div class="wrap nonav" role="document">
      <div class="content">
        <main class="main" id="main" role="main">
          @yield('content')
        </main>
      </div>
    </div>
    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
