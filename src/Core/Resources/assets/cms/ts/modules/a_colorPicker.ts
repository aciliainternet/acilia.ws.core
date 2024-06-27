import '../typings/vanillaPicker.d';
import Picker, { PickerColor } from 'vanilla-picker';

function init(elm: HTMLElement | null = null) {
  const picker = new Picker(elm);

  picker.onChange = (color: PickerColor) => {
    if (elm) {
      elm.dataset.value = color.hex;
      elm.style.background = color.rgbaString;
    }
  };
}

export default init;
