
export default {
  init() {
    // Curved Banner Text
    $('.curved').circleType({ fitText: true, radius: 500 });

    // Add video or fallback to image
    function isIE() {
      const myNav = navigator.userAgent.toLowerCase();
      return (myNav.indexOf('msie') !== -1) ? parseInt(myNav.split('msie')[1], 10) : false;
    }

    const isIEOld = isIE() && isIE() < 9;
    const isiPad = navigator.userAgent.match(/iPad/i);

    const img = $('#hero-video').data('placeholder');
    const video = $('#hero-video').data('video');
    const noVideo = $('#hero-video').data('src');

    let el = '';

    if ($(window).width() > 599 && !isIEOld && !isiPad) {
      el += `<video class="video-element" autoplay loop poster="${img}">`;
      el += `<source src="${video}" type="video/mp4">`;
      el += '</video>';
    } else {
      el = `<div class="video-element" style="background-image: url(${noVideo})"></div>`;
    }

    $('#hero-video').prepend(el);
  },
  finalize() {
    // JavaScript to be fired on the home page, after the init JS
  },
};
