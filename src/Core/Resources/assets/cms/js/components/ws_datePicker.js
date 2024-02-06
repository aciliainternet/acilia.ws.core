import { aDatePicker, getADatePickerInstance } from '../modules/a_datePicker';

/**
 * Adds an x to erase the contents of the input element
 *
 * @param {HTMLInputElement} elm
 */
function addEraseButton(elm) {
  const button = document.createElement('button');

  button.className = "ws_delete fa-solid fa-circle-xmark";
  // button.innerHTML = 'Remove Item';

  if (elm.value === '') {
    button.classList.add('hidden');
  }

  elm.insertAdjacentElement('afterend', button);

  getADatePickerInstance(elm).config.onChange.push((selectedDates, dateStr) => {
    if (selectedDates.length > 0) {
      button.classList.remove('hidden');
    } else {
      button.classList.add('hidden');
    }
  });

  button.addEventListener('click', (evt) => {
    evt.preventDefault();
    /* eslint-disable no-param-reassign */
    elm.value = '';
    button.classList.add('hidden');
  });
}

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
      const { format, defaultHour, mobileSupport } = elm.dataset;
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
      if (mobileSupport === 'disabled') {
        options.disableMobile = true;
      }
      aDatePicker(elm, options);
      addEraseButton(elm);
    }
  });
}

export default init;
