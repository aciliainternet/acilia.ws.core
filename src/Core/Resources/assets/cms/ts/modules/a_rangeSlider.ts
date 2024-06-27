import '../typings/rangeSlider.d';
import rangeSlider from 'rangeslider-pure';

const createTooltip = (
  rangeSliderEl: HTMLRangeSliderElement,
  tooltip: HTMLElement
) => {
  const handleEl = rangeSliderEl.handle;
  tooltip.classList.add('rangeSlider__tooltip');
  handleEl.appendChild(tooltip);

  tooltip.textContent = rangeSliderEl.value;
};

function init(
  elm: HTMLInputElement | null = null,
  options: Partial<RangeSliderOptions> = {}
) {
  if (elm === null) {
    return;
  }

  const opt: Partial<RangeSliderOptions> = {
    ...options,
    polyfill: true,
    root: document,
  };

  const tooltip = document.createElement('div');
  rangeSlider.create(elm, opt as RangeSliderOptions);
  createTooltip((elm as HTMLRangeSliderElement).rangeSlider, tooltip);
}

export default init;
