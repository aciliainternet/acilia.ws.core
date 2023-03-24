let elementBehind = null;

function show(element) {
  if (element) {
    elementBehind = element;
    elementBehind = elementBehind.querySelector('.js-loader') ? elementBehind : elementBehind.parentElement;
    elementBehind.querySelector('.js-loader').classList.add('is-active');
    elementBehind.classList.add('no-scroll');
  }
}

function hide() {
  if (elementBehind) {
    elementBehind.querySelector('.js-loader').classList.remove('is-active');
    elementBehind.classList.remove('no-scroll');
  }
}

export {
  show,
  hide,
};
