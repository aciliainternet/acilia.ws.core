// https://github.com/verlok/lazyload
import LazyLoad from 'vanilla-lazyload';

const threshold = 500;
const throttle = 5;

let imgLazyLoad = null;

function initLazyLoad() {
  imgLazyLoad = new LazyLoad({
    throttle,
    threshold,
    class_loaded: 'lazy-loaded',
    elements_selector: 'img[data-a-lazy]',
  });
}

function update() {
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
