import Modal from '../modules/a_modal';

const componentSelector = '[data-component="ws_extended_entity"]';

const modal = new Modal({
  autoOpen: false,
  updateURL: false,
  initLoad: false,
  maxWidth: '1200px',
  closeOnOverlay: true,
  closeButton: true,
  identifier: 'widget-list',
});

function openModal() {
  modal.open('.js-widget-list-modal');
}


async function showForm(event) {
  const elem = event.currentTarget;
  console.log(elem.dataset);
  const url = elem.dataset.modalUrl;
  console.log(url);
  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    console.log(response);
  } catch (e) {
    console.error(e);
  }
}

function init() {
  document.querySelectorAll(componentSelector).forEach((elm) => {
    elm.addEventListener('click', showForm);
  });
}

export default init;
