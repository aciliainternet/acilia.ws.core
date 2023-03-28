import { Controller } from '@hotwired/stimulus';

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

    const currentTarget = event.currentTarget as HTMLElement;

    if (!currentTarget) {
      return;
    }

    const { saveUrl } = currentTarget.dataset;

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
      });

      const body = await response.text();
      if (response.ok) {
        this.element.dispatchEvent(
          new CustomEvent('notifications:showSuccess', {
            bubbles: true,
            detail: { msg: JSON.parse(body).msg },
          })
        );
      } else {
        this.element.dispatchEvent(
          new CustomEvent('notifications:showError', {
            bubbles: true,
            detail: { msg: JSON.parse(body).msg },
          })
        );
      }
    } catch (e) {
      this.element.dispatchEvent(
        new CustomEvent('notifications:showError', {
          bubbles: true,
          detail: { msg: window.cmsTranslations.error },
        })
      );
    }
  }
}
