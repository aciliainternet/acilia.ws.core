import rangeSlider from 'rangeslider-pure';

const createTooltip = (rangeSliderEl: any, tooltip: HTMLElement) => {
  const handleEl = rangeSliderEl.handle;
  tooltip.classList.add('rangeSlider__tooltip');
  handleEl.appendChild(tooltip);
  tooltip.textContent = rangeSliderEl.value;
};

function init(elm: HTMLElement & { rangeSlider?: any } | null = null, options: any = {}) {
  if (elm === null) {
    return;
  }

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
