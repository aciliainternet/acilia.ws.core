import { Controller } from '@hotwired/stimulus';
import { showError, showSuccess } from '../modules/a_notifications';

export default class extends Controller {
  static targets = ['saveBtn', 'option'];

  static values = {
    saveUrl: String,
  };

  declare saveUrlValue: string;
  declare optionTargets: Array<HTMLSelectElement | HTMLInputElement>;
  declare saveBtnTarget: HTMLInputElement;

  async saveSettings() {
    const settings = {};

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
        showSuccess(responseData.msg);
      } else {
        showError(responseData.msg);
      }
    } catch (e) {
      showError(window.cmsTranslations.error);
    }
  }
}