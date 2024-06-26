import { CMSTranslations } from '../interfaces/translations';

declare global {
  type Window = {
    cmsTranslations: CMSTranslations;
  }
}
