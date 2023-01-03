import { Controller } from '@hotwired/stimulus';
import showAlert from '../modules/a_alert';

interface DeleteResponseJson {
  id: number;
  msg: string;
  title: string;
  href: string;
}

export default class extends Controller {
  static values = {
    id: Number,
    title: String,
    url: String,
    message: String,
  };

  declare idValue: number;
  declare titleValue: string;
  declare urlValue: string;
  declare messageValue: string;

  onRemoveDone(responseData: DeleteResponseJson) {  
    if (responseData.id) {
      this.element.classList.add('js-genericDelete_remove');
    }

    showAlert({
      title: responseData.title,
      text: responseData.msg,
      icon: 'success',
    }, () => {
      this.element.classList.add('animated', 'fadeOutRight');
      setTimeout(() => {
        this.element.remove();
      }, 800);

      const pagSubtotalValue = document.getElementById('js-pagination_subtotal');
      if (pagSubtotalValue) {
        pagSubtotalValue.textContent = (parseInt(pagSubtotalValue.textContent || '0', 10) - 1).toString();
      }

      const pagTotalValue = document.getElementById('js-pagination_total');
      if (pagTotalValue) {
        pagTotalValue.textContent = (parseInt(pagTotalValue.textContent || '0', 10) - 1).toString();
      }
    });
  }

  async sendDeletePost() {
    try {
      const response = await fetch(this.urlValue, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: this.idValue }),
      });

      const responseData = await response.json() as DeleteResponseJson;

      if (response.ok) {
        if (response.status === 302) {
          showAlert({
            title: responseData.title,
            text: responseData.msg,
            icon: 'success',
          });

          if (responseData.href) {
            setTimeout(() => {
              window.location.href = responseData.href;
            }, 2000);
          }
        }

        if (response.status === 200) {
          this.onRemoveDone(responseData);
        }
      } else {
        showAlert(responseData.msg);
      }
    } catch (e) {
      showAlert(window.cmsTranslations.error);
    }
  }

  remove(event: MouseEvent) {
    if (!event.currentTarget) {
      return;
    }

    showAlert({
      icon: 'warning',
      dangerMode: true,
      title: this.titleValue,
      text: this.messageValue,
      buttons: {
        cancel: {
          text: window.cmsTranslations.cancel,
          closeModal: true,
        },
        confirm: {
          text: window.cmsTranslations.delete.confirm,
          value: this.idValue,
          closeModal: false,
        },
      },
    }, () => {
      this.sendDeletePost();
    });
  }
}
