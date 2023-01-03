import swal from 'sweetalert';
import { SwalOptions } from 'sweetalert/typings/modules/options';

type SwalCallback = ((value: any) => void) | null;

function showAlert(options: string | Partial<SwalOptions> | null = null, callback: SwalCallback = null) {
  if (options && callback) {
    swal(options).then(callback);
  } else if (options) {
    swal(options);
  }
}

export default showAlert;
