import Modal from '../modules/a_modal';

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

function init() {
  if (document.querySelector('.js-open-modal-widget-list')) {
    document.querySelector('.js-open-modal-widget-list').addEventListener('click', openModal);
  }
}

export default init;
