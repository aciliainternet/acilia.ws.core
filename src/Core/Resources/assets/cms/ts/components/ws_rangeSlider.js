import aRangeSlider from '../modules/a_rangeSlider';

function init() {
  document.querySelectorAll('[data-component="ws_range-slider"]').forEach((elm) => {
    const options = {
      min: parseInt(elm.dataset.min),
      max: parseInt(elm.dataset.max),
      step: parseInt(elm.dataset.step),
    };
    aRangeSlider(elm, options);
  });
}

export default init;
