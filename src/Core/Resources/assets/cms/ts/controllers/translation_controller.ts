import { Controller } from '@hotwired/stimulus';
import { showError, showSuccess } from '../modules/a_notifications';

export default class extends Controller {
  static targets = ['save', 'attribute'];

  declare saveTarget: HTMLElement;
  declare attributeTargets: HTMLTextAreaElement[];

  connect() {    
    this.saveTranslations = this.saveTranslations.bind(this);
    this.saveTarget.addEventListener('click', this.saveTranslations);
  }

  disconnect() {
    this.saveTarget.removeEventListener('click', this.saveTranslations);
  }

  async saveTranslations(event: MouseEvent) {
    const translations: Record<string, string> = {};

    this.attributeTargets.forEach((item) => {
      const name = item.getAttribute('name');

      if (name) {
        translations[name] = item.value;
      }
    });

    const currentTarget = event.currentTarget;

    if (!currentTarget) {
      return;
    }

    const saveUrl = (currentTarget as HTMLElement).dataset.saveUrl;

    if (!saveUrl) {
      return;
    }

    try {
      const response = await fetch(saveUrl, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(translations),
      })
      
      const body = await response.text();
      if (response.ok) {
        showSuccess(JSON.parse(body).msg);
      } else {
        showError(JSON.parse(body).msg);
      }
    } catch (e) {
      showError(window.cmsTranslations.error);
    }
  }
}
