import '../typings/global.d';
import { Controller } from '@hotwired/stimulus';
import {
  DatePickerOptions,
  DatePickerLocale,
  HTMLPickrElement,
  aDatePicker,
  getADatePickerInstance
} from '../modules/a_datePicker';

export default class extends Controller<HTMLInputElement> {
  connect() {
    const { cmsSettings } = window;
    if (cmsSettings === undefined || cmsSettings === null) {
      throw Error('No CMS Settings defined.');
    }

    const datePickerCMSConfig = cmsSettings.ws_cms_components.datepicker;
  
    if (!this.element.dataset.wsDisable) {
      const options: DatePickerOptions = {
        locale: cmsSettings.locale as DatePickerLocale,
      };

      const { format, defaultHour } = this.element.dataset;

      if (format && Object.prototype.hasOwnProperty.call(datePickerCMSConfig.format, format)) {
        // if the format from the input exist in the configuration json for the component, we assign it
        options.dateFormat = datePickerCMSConfig.format[format];
        if (format === 'date_hour') {
          options.enableTime = true;
        }
        if (format === 'hour') {
          options.enableTime = true;
          options.noCalendar = true;
        }
      }

      if (defaultHour) {
        options.defaultHour = Number(defaultHour);
      }
      aDatePicker(this.element, options);
      this.addEraseButton();
    }
  }

  /**
   * Adds an x to erase the contents of the input element
   */
  addEraseButton() {
    const button = document.createElement('button');

    button.className = 'choices__button ws_delete';
    button.innerHTML = 'Remove Item';

    if (this.element.value === '') {
      button.classList.add('hidden');
    }

    this.element.insertAdjacentElement('afterend', button);

    getADatePickerInstance(this.element as HTMLPickrElement).config.onChange.push((selectedDates, dateStr) => {
      if (selectedDates.length > 0) {
        button.classList.remove('hidden');
      } else {
        button.classList.add('hidden');
      }
    });

    button.addEventListener('click', (evt) => {
      evt.preventDefault();
      /* eslint-disable no-param-reassign */
      this.element.value = '';
      button.classList.add('hidden');
    });
  }
}
