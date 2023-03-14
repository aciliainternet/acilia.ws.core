// https://github.com/verlok/lazyload
import LazyLoad from 'vanilla-lazyload';

const threshold = 500;

let imgLazyLoad: typeof LazyLoad | null = null;

function initLazyLoad() {
  imgLazyLoad = new LazyLoad({
    threshold,
    class_loaded: 'lazy-loaded',
    elements_selector: 'img[data-a-lazy]',
  });
}

function update() {
  if (imgLazyLoad === null) {
    return;
  }

  imgLazyLoad.update();
}

function init() {
  initLazyLoad();
  window.addEventListener('resize', update);
  document.addEventListener('DOMContentLoaded', update);
}

export {
  init,
  update,
};
