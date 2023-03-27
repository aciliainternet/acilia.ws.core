interface DatePickerFormat {
  [index: string]: string;
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
  markdown_asset_file: { endpoint: string };
  markdown_asset_image: { endpoint: string };
  assets_images: { endpoint: string };
}

export interface CMSSettings {
  ws_cms_components: WSComponentSettings;
  locale: string;
}
