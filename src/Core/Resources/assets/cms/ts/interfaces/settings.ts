interface DatePickerFormat {
  date_hour: string;
  date: string;
  hour: string;
}

interface DatePickerSettings {
  format: DatePickerFormat;
}

interface InputMultipleSettings {
  duplicate_items_allowed: boolean;
  edit_items: boolean;
  placeholder: boolean;
  remove_item_button: boolean;
}

interface WSComponentSettings {
  datepicker: DatePickerSettings;
  input_multiple: InputMultipleSettings;
}

export interface CMSSettings {
  ws_cms_components: WSComponentSettings;
  locale: string;
}
