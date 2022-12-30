import { CMSTranslations } from '../interfaces/translations';

declare global {
  interface Window {
    cmsTranslations: CMSTranslations;
  }
}
