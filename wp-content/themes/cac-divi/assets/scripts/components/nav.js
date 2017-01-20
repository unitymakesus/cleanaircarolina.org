export default class Nav {

  constructor() {
    Nav.checkScrollTop();

    $(window).scroll(() => {
      Nav.checkScrollTop();
    });
  }

  static checkScrollTop() {
    const action = $(window).scrollTop() > 90 ? 'addClass' : 'removeClass';
    $('header.main')[action]('active');
  }
}
