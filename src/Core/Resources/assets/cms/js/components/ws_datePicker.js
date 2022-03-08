import { aDatePicker } from '../modules/a_datePicker';

function init() {
  const { cmsSettings } = window;
  if (cmsSettings === undefined || cmsSettings === null) {
    throw Error('No CMS Settings defined.');
  }

  const datePickerCMSConfig = cmsSettings.ws_cms_components.datepicker;

  document.querySelectorAll('[data-component="ws_datepicker"]').forEach((elm) => {
    if (!elm.dataset.wsDisable) {
      const options = {
        locale: cmsSettings.locale,
      };
      const { format, defaultHour } = elm.dataset;
      if (format && Object.prototype.hasOwnProperty.call(datePickerCMSConfig.format, format)) {
        // if the format from the input exist in the configuration json for the component, we assig it
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
        options.defaultHour = defaultHour;
      }
      aDatePicker(elm, options);
    }
  });
}

export default init;
