/* eslint no-underscore-dangle: ["error", { "allow": ["_flatpickr"] }] */

/*
*
* a_datePicker.js v1.0.2
*
*
*/

import { Spanish } from 'flatpickr/dist/l10n/es';
import flatpickr from 'flatpickr';

function aDatePicker(elm = null, options = null) {
  let datepicker = null;
  const value = elm.value ?? null;
  if (elm && options) {
    if (options.locale === 'es' || options.locale === 'ES') {
      options.locale = Spanish;
    }
    datepicker = flatpickr(elm, options);
  } else if (elm) {
    datepicker = flatpickr(elm);
  }

  if (value && typeof datepicker.setDate === "function") {
    datepicker.setDate(value);
  }

  return datepicker;
}

function getADatePickerInstance(selector) {
  return document.querySelector(selector)._flatpickr;
}

export {
  aDatePicker,
  getADatePickerInstance,
};
