import { CMSTranslations } from '../interfaces/translations';
import { CMSSettings } from '../interfaces/settings';

declare global {
  interface Window {
    cmsTranslations: CMSTranslations;
    cmsSettings: CMSSettings;
  }
}
