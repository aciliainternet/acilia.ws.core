import Picker from 'vanilla-picker';

function init(elm = null) {
  const picker = new Picker(elm);

  picker.onChange = function (color) {
    elm.dataset.value = color.hex;
    elm.style.background = color.rgbaString;
  };
}

export default init;
