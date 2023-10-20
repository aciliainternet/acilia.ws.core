import showAlert from './a_alert';
import showSngAlert from './a_alert-v2';

function manageActionsSelector(show) {
  const selector = document.querySelector('.js-batchActions');
  if (show) {
    selector.classList.remove('u-hidden');
  } else {
    selector.classList.add('u-hidden');
  }
}

function onBatchActionDone(event) {
  const request = event.currentTarget;
  const response = JSON.parse(request.response);

  switch (request.status) {
    case 403:
    case 404:
    case 400:
    case 500:
      showSngAlert({
        title: window.cmsTranslations.error,
        text: response.msg,
        icon: 'error',
      });
      break;
    case 200:
      showSngAlert({
        text: response.msg,
        icon: 'success',
      }, () => {
        window.location.reload();
      });
      break;
    default:
      break;
  }
}

function batchAction(url) {
  const ids = [];
  document.querySelectorAll('input[type=checkbox]:checked').forEach((input) => {
    const wrapper = input.closest('tr');
    if (wrapper.dataset.id) {
      ids.push(wrapper.dataset.id);
    }
  });

  if (ids.length === 0) {
    showSngAlert({
      title: window.cmsTranslations.error,
      text: window.cmsTranslations.ws_cms_batch_actions.no_item_selected,
      icon: 'error',
    });
  } else if (url !== null) {
    const xhr = new XMLHttpRequest();

    xhr.open('POST', url);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = onBatchActionDone;
    xhr.onerror = onBatchActionDone;
    xhr.send(JSON.stringify({ ids }));
  }
}

function handleAction(event) {
  const selectInput = event.currentTarget;
  let url = null;
  let title = '';

  if (selectInput.options[selectInput.selectedIndex]) {
    url = selectInput.options[selectInput.selectedIndex].value;
    ({ title } = selectInput.options[selectInput.selectedIndex].dataset);
  }

  if (url && url.length > 0) {
    showSngAlert({
      icon: 'warning',
      dangerMode: true,
      title,
      text: window.cmsTranslations.ws_cms_batch_actions.confirm_message,
      buttons: {
        cancel: window.cmsTranslations.cancel,
        confirm: {
          text: window.cmsTranslations.ws_cms_batch_actions.confirm_button_label,
          value: url,
          closeModal: false,
        },
      },
    }, (value) => {
      batchAction(value);
    });
  }
}

function handleItem() {
  const allInput = document.querySelector('.js-batchAll');
  if (allInput && allInput.checked) {
    allInput.checked = false;
  }

  const selectedCheckboxes = document.querySelectorAll('input[type=checkbox]:checked');
  const anySelected = !(selectedCheckboxes.length === 0);
  manageActionsSelector(anySelected);
}

function handleAll(event) {
  const state = event.currentTarget.checked;
  document.querySelectorAll('.js-batchItem').forEach((input) => {
    const checkbox = input;
    checkbox.checked = state;
  });

  manageActionsSelector(state);
}

function init() {
  if (document.querySelector('.js-batchAll')) {
    document.querySelector('.js-batchAll').addEventListener('click', handleAll);
  }

  if (document.querySelector('.js-batchAction')) {
    document.querySelector('.js-batchAction').addEventListener('change', handleAction);
  }

  document.querySelectorAll('.js-batchItem')
    .forEach((input) => input.addEventListener('change', handleItem));
}

export default init();
