export default class Nav {

  constructor() {
    Nav.checkScrollTop();

    $(window).scroll(() => {
      Nav.checkScrollTop();
    });

    /**
     * Focus styles for menus when using keyboard navigation
     */
    $('[role="navigation"]').on('focus.aria  mouseenter.aria', '[aria-haspopup="true"]', (ev) => {
      $(ev.currentTarget).attr('aria-expanded', true);
    });

    $('[role="navigation"]').on('blur.aria  mouseleave.aria', '[aria-haspopup="true"]', (ev) => {
      $(ev.currentTarget).attr('aria-expanded', false);
    });
  }

  static checkScrollTop() {
    const action = $(window).scrollTop() > 90 ? 'addClass' : 'removeClass';
    $('header.main')[action]('active');
    $('#back-to-top')[action]('active');
  }
}
