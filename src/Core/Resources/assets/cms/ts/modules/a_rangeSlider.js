import rangeSlider from 'rangeslider-pure';

const createTooltip = (rangeSliderEl, tooltip) => {
  const handleEl = rangeSliderEl.handle;
  tooltip.classList.add('rangeSlider__tooltip');
  handleEl.appendChild(tooltip);
  tooltip.textContent = rangeSliderEl.value;
};

function init(elm = null, options = {}) {
  const opt = {
    ...options,
    polyfill: true,
    root: document,
  };
  const tooltip = document.createElement('div');
  rangeSlider.create(elm, opt);
  createTooltip(elm.rangeSlider, tooltip);
}

export default init;
