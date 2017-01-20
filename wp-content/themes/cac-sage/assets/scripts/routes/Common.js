import Nav from '../components/nav';

export default {
  init() {
    window.nav = new Nav();
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
