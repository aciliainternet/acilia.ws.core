export interface SelectTranslations {
  loading: string;
  no_results: string;
  no_choices: string;
  item_select: string;
}

export interface InputMultipleTranslations {
  add_item: string;
  unique_item: string;
  invalid_item: string;
};

export interface MarkdownTranslations {
  autosave: string;
}

export interface CropperTranslations {
  error?: string;
}

export interface AssetsImagesTranslations {
  no_results: string;
}

export interface CMSComponentTranslations {
  assets_images: AssetsImagesTranslations;
  select: SelectTranslations;
  input_multiple: InputMultipleTranslations;
  markdown: MarkdownTranslations;
  cropper: CropperTranslations;
}

export interface CMSBatchActionsTranslations {
  confirm_message: string;
  confirm_button_label: string;
  no_item_selected: string;
}

export interface CMSTranslations {
  ws_cms_components: CMSComponentTranslations;
  ws_cms_batch_actions: CMSBatchActionsTranslations;
  cancel: string;
  delete: { confirm: string; };
  error: string;
}
