export interface SelectTranslations {
  loading: string;
  no_results: string;
  no_choices: string;
  item_select: string;
}

export interface CMSComponentTranslations {
  select: SelectTranslations;
}

export interface CMSTranslations {
  ws_cms_components: CMSComponentTranslations;
  cancel: string;
  delete: { confirm: string; };
  error: string;
}
