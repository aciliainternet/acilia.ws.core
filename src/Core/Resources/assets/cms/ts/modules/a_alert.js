import swal from 'sweetalert';

function showAlert(options = null, callback = null) {
  if (options && callback) {
    swal(options).then(callback);
  } else if (options) {
    swal(options);
  }
}

export default showAlert;
