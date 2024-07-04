import { showError, showSuccess } from '../modules/a_notifications';
import '../modules/sidebar';

function onSaveTranslations(event) {
  const xhr = event.currentTarget;

  if (xhr.readyState === 4) {
    if (xhr.status === 200) {
      showSuccess(JSON.parse(xhr.response).msg);
    } else if ((xhr.status === 400 || xhr.status === 500)) {
      showError(JSON.parse(xhr.response).msg);
    }
  }
}

function saveTranslations(event) {
  const translations = {};

  document.querySelectorAll('.ws-translation-attribute').forEach((item) => {
    translations[item.getAttribute('name')] = item.value;
  });

  const xhr = new XMLHttpRequest();
  xhr.open('POST', event.currentTarget.getAttribute('data-save-url'));
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest', 'Content-Type', 'application/json');
  xhr.onreadystatechange = onSaveTranslations;
  xhr.send(JSON.stringify(translations));
}

function init() {
  if (document.getElementById('ws-translation-save')) {
    document.getElementById('ws-translation-save').addEventListener('click', saveTranslations);
  }
}

export default init;
