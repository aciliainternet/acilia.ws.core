import Picker from 'vanilla-picker';

export interface Color {
  hex: string;
  rgbaString: string;
}

function init(elm: HTMLElement | null = null) {
  const picker = new Picker(elm);

  picker.onChange = function (color: Color) {
    if (elm) {
      elm.dataset.value = color.hex;
      elm.style.background = color.rgbaString;
    }
  };
}

export default init;
