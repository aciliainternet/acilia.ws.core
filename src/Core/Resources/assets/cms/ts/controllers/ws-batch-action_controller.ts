import { Controller } from '@hotwired/stimulus';
import showAlert from '../modules/a_alert';

export default class extends Controller {
  static targets = ['allInput', 'itemInput', 'actions'];

  declare allInputTarget: HTMLInputElement;

  declare itemInputTargets: HTMLInputElement[];

  declare actionsTarget: HTMLSelectElement;

  declare urlValue: string;

  handleAction(event: Event) {
    const selectInput = event.currentTarget as HTMLSelectElement;
    let url: string | null = null;
    let title: string | undefined = '';

    if (selectInput.options[selectInput.selectedIndex]) {
      url = selectInput.options[selectInput.selectedIndex].value;
      const selectInputTitles = document.querySelector('[data-ws-batch-action-titles]') as HTMLElement;
      const selectedInputTitle = selectInputTitles.dataset.wsBatchActionTitles;
      title = selectedInputTitle ? JSON.parse(selectedInputTitle)[selectInput.selectedIndex] : '';
    }

    if (url && url.length > 0) {
      this.urlValue = url;
      showAlert(
        {
          icon: 'warning',
          dangerMode: true,
          title,
          text: window.cmsTranslations.ws_cms_batch_actions.confirm_message,
          buttons: {
            cancel: window.cmsTranslations.cancel,
            confirm: {
              text: window.cmsTranslations.ws_cms_batch_actions.confirm_button_label,
              value: url,
            },
          },
        },
        () => {
          this.batchAction();
        }
      );
    }
  }

  handleItem() {
    if (this.allInputTarget && this.allInputTarget.checked) {
      this.allInputTarget.checked = false;
    }

    const selectedCheckboxes = document.querySelectorAll(
      'input[type=checkbox]:checked'
    );

    const anySelected = !(selectedCheckboxes.length === 0);
    this.manageActionsSelector(anySelected);
  }

  handleAll(event: Event) {
    if (!event.currentTarget) {
      return;
    }

    const state = (event.currentTarget as HTMLInputElement).checked;

    this.itemInputTargets.forEach((input) => {
      const checkbox = input;
      checkbox.checked = state;
    });

    this.manageActionsSelector(state);
  }

  manageActionsSelector(show: boolean) {
    if (show) {
      this.actionsTarget.classList.remove('u-hidden');
    } else {
      this.actionsTarget.classList.add('u-hidden');
    }
  }

  onBatchActionDone(event: Event) {
    const request = event.currentTarget as XMLHttpRequest;
    const response = JSON.parse(request.response);

    switch (request.status) {
      case 403:
      case 404:
      case 400:
      case 500:
        showAlert({
          title: window.cmsTranslations.error,
          text: response.msg,
          icon: 'error',
        });
        break;
      case 200:
        showAlert(
          {
            text: response.msg,
            icon: 'success',
          },
          () => {
            window.location.reload();
          }
        );
        break;
      default:
        break;
    }
  }

  async batchAction() {
    const ids: string[] = [];
    document
      .querySelectorAll('input[type=checkbox]:checked')
      .forEach((input) => {
        const wrapper = input.closest('tr');
        if (wrapper && wrapper.dataset.id) {
          ids.push(wrapper.dataset.id);
        }
      });

    if (ids.length === 0) {
      showAlert({
        title: window.cmsTranslations.error,
        text: window.cmsTranslations.ws_cms_batch_actions.no_item_selected,
        icon: 'error',
      });
    } else if (this.urlValue !== null) {
      try {
        const response = await fetch(this.urlValue, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ ids }),
        });

        const responseData: { msg: string } = await response.json();
        if (response.ok) {
          showAlert(
            {
              text: responseData.msg,
              icon: 'success',
            },
            () => {
              window.location.reload();
            }
          );
        } else {
          showAlert({
            title: window.cmsTranslations.error,
            text: responseData.msg,
            icon: 'error',
          });
        }
      } catch (e) {
        showAlert({
          title: window.cmsTranslations.error,
          text: window.cmsTranslations.error,
          icon: 'error',
        });
      }
    }
  }
}
