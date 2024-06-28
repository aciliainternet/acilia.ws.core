import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['saveBtn', 'option'];

  static values = {
    saveUrl: String,
  };

  declare saveUrlValue: string;

  declare optionTargets: Array<HTMLSelectElement | HTMLInputElement>;

  declare saveBtnTarget: HTMLInputElement;

  async saveSettings() {
    const settings: { [index: string]: string } = {};

    this.optionTargets.forEach((item) => {
      if (item.type === 'checkbox' && !(item as HTMLInputElement).checked) {
        item.value = '0';
      }

      const nameAttr = item.getAttribute('name');
      if (nameAttr) {
        settings[nameAttr] = item.value;
      }
    });

    try {
      const response = await fetch(this.saveUrlValue, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(settings),
      });

      const responseData = await response.json();

      if (response.ok) {
        this.element.dispatchEvent(
          new CustomEvent('notifications:showSuccess', {
            bubbles: true,
            detail: { msg: responseData.msg },
          })
        );
      } else {
        this.element.dispatchEvent(
          new CustomEvent('notifications:showError', {
            bubbles: true,
            detail: { msg: responseData.msg },
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
