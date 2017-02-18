import Nav from '../components/nav';

export default {
  init() {
    window.nav = new Nav();

    // Determine trigger for touch/click events
    let clickortap = 'click';
    if ($('html').hasClass('touch')) {
      clickortap = 'touchend';
    }

    function showFocus() {
      $('#search-popover').addClass('visible').find('.search-field').focus();
    }

    function timeoutHide() {
      $('#search-popover').removeClass('visible');
    }

    function blurTimeout() {
      window.setTimeout(timeoutHide, 200);
    }

    // Toggle search button to show search field
    $('#search-action').on(clickortap, showFocus);

    // Hide header search field when it loses focus
    $('#search-popover input').on('blur', blurTimeout);
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
