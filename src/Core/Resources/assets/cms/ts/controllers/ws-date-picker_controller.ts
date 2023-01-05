import { Controller } from '@hotwired/stimulus';
import { DatePickerOptions, DatePickerLocale, aDatePicker } from '../modules/a_datePicker';

export default class extends Controller<HTMLElement> {
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
    }
  }
}
