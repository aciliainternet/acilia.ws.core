import toastr from 'toastr';

const defaultOptions = {
  closeButton: true,
  debug: false,
  progressBar: true,
  preventDuplicates: false,
  positionClass: 'toast-top-right',
  onclick: null,
  showDuration: '400',
  hideDuration: '1000',
  timeOut: '7000',
  extendedTimeOut: '1000',
  showEasing: 'swing',
  hideEasing: 'linear',
  showMethod: 'fadeIn',
  hideMethod: 'fadeOut',
};

function showError(msg, title = null, options = null) {
  if (title && options) {
    toastr.error(msg, title, options);
  }
  if (title) {
    toastr.error(msg, title);
  }
  toastr.error(msg);
}

function showSuccess(msg, title = null, options = null) {
  if (title && options) {
    toastr.success(msg, title, options);
  }
  if (title) {
    toastr.success(msg, title);
  }
  toastr.success(msg);
}

function checkNotifications(selector) {
  document.querySelectorAll(selector).forEach((elem) => {
    if (elem.dataset.type === 'success') {
      showSuccess(elem.innerHTML);
    }
    if (elem.dataset.type === 'failure') {
      showError(elem.innerHTML);
    }
  });
}

function init(notificationClass = '.cms-notifications', options = null) {
  toastr.options = options || defaultOptions;
  checkNotifications(notificationClass);
}

export {
  init,
  showError,
  showSuccess,
};
