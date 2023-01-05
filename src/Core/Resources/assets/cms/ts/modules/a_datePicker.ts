/* eslint no-underscore-dangle: ["error", { "allow": ["_flatpickr"] }] */

/*
*
* a_datePicker.ts v1.0.2
*
*
*/

import { Spanish } from 'flatpickr/dist/l10n/es';
import flatpickr from 'flatpickr';
import { Instance } from 'flatpickr/dist/types/instance';
import { BaseOptions } from 'flatpickr/dist/types/options';
import { key as LocaleKey } from 'flatpickr/dist/types/locale';

export type DatePickerOptions = Partial<BaseOptions>;
export type DatePickerLocale = LocaleKey;

function aDatePicker(elm: string | NodeList | HTMLElement | null = null, options: DatePickerOptions | null = null) {
  let datepicker: Instance | Instance[] | null = null;
  if (elm && options) {
    if (options.locale === 'es') {
      options.locale = Spanish;
    }
    datepicker = flatpickr(elm, options);
  } else if (elm) {
    datepicker = flatpickr(elm);
  }
  return datepicker;
}

function getADatePickerInstance(selector: string) {
  return (document.querySelector(selector) as any)?._flatpickr as Instance;
}

export {
  aDatePicker,
  getADatePickerInstance,
};
