import { CMSTranslations } from '../interfaces/translations';
import { CMSSettings } from '../interfaces/settings';
import { Application } from '@hotwired/stimulus';

declare global {
  interface Window {
    cmsTranslations: CMSTranslations;
    cmsSettings: CMSSettings;
    Stimulus?: Application;
  }
}
