import { Controller } from '@hotwired/stimulus';
import toastr from 'toastr';
import { showError, showSuccess } from '../modules/a_notifications';

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

export default class extends Controller {
  static values = {
    notificationClass: {
      type: String,
      default: 'cms-notifications'
    },
    options: {
      type: Object,
      default: null,
    },
  };

  declare notificationClassValue: string;
  declare optionsValue: any | null;
  
  connect() {      
    toastr.options = this.optionsValue || defaultOptions;
    this.checkNotifications();
  }

  checkNotifications() {
    document.querySelectorAll<HTMLElement>(`.${this.notificationClassValue}`).forEach((elem) => {
      if (elem.dataset.type === 'success') {
        showSuccess(elem.innerHTML);
      }
      if (elem.dataset.type === 'failure') {
        showError(elem.innerHTML);
      }
    });
  }
}
