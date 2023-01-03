import toastr from 'toastr';

function showError(msg: string, title: string | null = null, options: any | null = null) {
  if (title && options) {
    toastr.error(msg, title, options);
  }
  if (title) {
    toastr.error(msg, title);
  }
  toastr.error(msg);
}

function showSuccess(msg: string, title: string | null = null, options: any | null = null) {
  if (title && options) {
    toastr.success(msg, title, options);
  }
  if (title) {
    toastr.success(msg, title);
  }
  toastr.success(msg);
}

export {
  showError,
  showSuccess,
};
