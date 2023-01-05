interface DatePickerFormat {
  date_hour: string;
  date: string;
  hour: string;
}

interface DatePickerSettings {
  format: DatePickerFormat;
}

export interface CMSSettings {
  ws_cms_components: { datepicker: DatePickerSettings };
  locale: string;
}
