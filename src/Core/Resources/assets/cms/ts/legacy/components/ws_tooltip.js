import aTooltip from '../modules/a_tooltip';

const basicConfig = {
  arrow: true,
  animation: 'fade',
};

function init() {
  const tooltips = document.querySelectorAll('[data-tippy]');
  aTooltip(tooltips, basicConfig);
}

export default init;
