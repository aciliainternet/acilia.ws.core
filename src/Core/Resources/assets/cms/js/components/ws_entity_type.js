import Modal from '../modules/a_modal';

const componentSelector = '[data-component="ws-entity-type"]';
let dataset;

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
  dataset = elem.dataset;
  const url = dataset.url;

  const data = new URLSearchParams();
  if (dataset.formclass) {
    data.append('formClass', dataset.formclass);
  }

  fetch(url, {
    method: 'POST',
    body: data
  }).then(function (response) {
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

  const parser = new DOMParser();
  const doc = parser.parseFromString(html, "text/html");

  modalContainer.replaceChildren(doc.forms[0]);
  initForm(modalContainer);

  modal.open('#ws-entity-type-modal');
}

function submitButtonClick(event) {
  event.preventDefault();
  const submitButton =  event.currentTarget;
  const form = submitButton.closest('form');
  submitForm(form);
}

function submitForm(form) {

  const data = new URLSearchParams();
  for (const pair of new FormData(form)) {
      data.append(pair[0], pair[1]);
  }
  data.append('formClass', dataset.formclass);

  fetch(dataset.url, {
    method: 'POST',
    body: data
  }).then(function (response) {
    if (response.status == 201) {
      response.json().then( function (json) {
        console.log(json);
      });
      // close modal
      // load new entities and populate select
      return;
    } else if ( response.status == 500 ) {
      console.error(response.json());
      return;
    }

    response.text().then(function (html) {
      processResponse(html);
    });

  }).catch(function (err) {
    console.warn('Something went wrong.', err);
  });
}

function cancelForm() {
  modal.close();
}

function initForm(container) {
  container.querySelectorAll('[type="submit"]').forEach((elm) => {
    elm.addEventListener('click', submitButtonClick);
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
