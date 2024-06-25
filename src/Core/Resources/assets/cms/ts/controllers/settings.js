import { showError, showSuccess } from '../modules/a_notifications';

function onSaveSettings(event) {
  const xhr = event.currentTarget;

  if (xhr.readyState === 4) {
    if (xhr.status === 200) {
      showSuccess(JSON.parse(xhr.response).msg);
    } else if ((xhr.status === 400 || xhr.status === 500)) {
      showError(JSON.parse(xhr.response).msg);
    }
  }
}

function saveSettings(event) {
  const settings = {};

  document.querySelectorAll('.ws-setting-option').forEach((item) => {
    if (item.type === 'checkbox' && !item.checked) {
      item.value = 0;
    }
    settings[item.getAttribute('name')] = item.value;
  });

  const xhr = new XMLHttpRequest();
  xhr.open('POST', event.currentTarget.getAttribute('data-save-url'));
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest', 'Content-Type', 'application/json');
  xhr.onreadystatechange = onSaveSettings;
  xhr.send(JSON.stringify(settings));
}

function init() {
  if (document.getElementById('ws-settings-save')) {
    document.getElementById('ws-settings-save').addEventListener('click', saveSettings);
  }
}

export default init;
