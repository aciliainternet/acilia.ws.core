import { Controller } from '@hotwired/stimulus';
import toastr from 'toastr';
import { showError, showSuccess } from '../modules/a_notifications';

interface NotificationDetail {
  msg: string;
  title?: string;
  options?: ToastrOptions;
}

const defaultOptions: ToastrOptions = {
  closeButton: true,
  debug: false,
  progressBar: true,
  preventDuplicates: false,
  positionClass: 'toast-top-right',
  onclick: undefined,
  showDuration: 400,
  hideDuration: 1000,
  timeOut: 7000,
  extendedTimeOut: 1000,
  showEasing: 'swing',
  hideEasing: 'linear',
  showMethod: 'fadeIn',
  hideMethod: 'fadeOut',
};

export default class extends Controller {
  static values = {
    notificationClass: {
      type: String,
      default: 'cms-notifications',
    },
    options: {
      type: Object,
      default: defaultOptions,
    },
  };

  declare notificationClassValue: string;

  declare optionsValue: ToastrOptions;

  connect() {
    toastr.options = this.optionsValue;
    this.checkNotifications();

    this.showSuccess = this.showSuccess.bind(this);
    this.showError = this.showError.bind(this);

    this.element.addEventListener(
      'notifications:showSuccess',
      this.showSuccess
    );

    this.element.addEventListener('notification:showError', this.showError);
  }

  disconnect() {
    this.element.removeEventListener(
      'notifications:showSuccess',
      this.showSuccess
    );

    this.element.removeEventListener('notification:showError', this.showError);
  }

  checkNotifications() {
    document
      .querySelectorAll<HTMLElement>(`.${this.notificationClassValue}`)
      .forEach((elem) => {
        if (elem.dataset.type === 'success') {
          showSuccess(elem.innerHTML);
        }
        if (elem.dataset.type === 'failure') {
          showError(elem.innerHTML);
        }
      });
  }

  showSuccess(event: Event) {
    const { msg, title, options } = (event as CustomEvent)
      .detail as NotificationDetail;
    showSuccess(msg, title, options);
  }

  showError(event: Event) {
    const { msg, title, options } = (event as CustomEvent)
      .detail as NotificationDetail;
    showError(msg, title, options);
  }
}
