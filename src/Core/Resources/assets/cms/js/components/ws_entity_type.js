import Modal from '../modules/a_modal';

const componentSelector = '[data-component="ws-entity-type"]';

const modal = new Modal({
  autoOpen: false,
  updateURL: false,
  initLoad: false,
  maxWidth: '1200px',
  closeOnOverlay: true,
  closeButton: true,
  identifier: 'ws-entity-type-modal-container',
});

function loadForm(event) {

  event.preventDefault();
  const elem = event.currentTarget;
  const url = elem.dataset.url;

  fetch(url).then(function (response) {
    // html response
    return response.text();

  }).then(function (html) {
    processResponse(html);

  }).catch(function (err) {
    console.warn('Something went wrong.', err);
  });
}

function processResponse(html) {

  const modalContainer = document.getElementById('ws-entity-type-modal');
  console.log(modalContainer);
  modalContainer.innerHTML = html;

  initForm(modalContainer);

  modal.open('#ws-entity-type-modal');
}

function submitForm(event) {
  console.log('submitForm');
  const submitButton =  event.currentTarget;
  console.log(submitButton);
  const form = submitButton.closest('form');
  console.log(form);
}

function cancelForm() {
  modal.close();
}

function initForm(container) {
  container.querySelectorAll('[type="submit"]').forEach((elm) => {
    elm.addEventListener('click', submitForm);
  });
  container.querySelectorAll('[type="cancel"]').forEach((elm) => {
    elm.addEventListener('click', cancelForm);
  });
}

function init() {
  document.querySelectorAll(componentSelector).forEach((elm) => {
    elm.addEventListener('click', loadForm);
  });
}

export default init;
