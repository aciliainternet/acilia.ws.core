import aColorPicker from '../modules/a_colorPicker';

function init() {
  document.querySelectorAll('[data-component="ws_color-picker"]').forEach((elm) => {
    aColorPicker(elm);
  });
}

export default init;
