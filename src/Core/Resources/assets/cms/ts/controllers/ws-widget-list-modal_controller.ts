import { Controller } from '@hotwired/stimulus';
import Modal from '../modules/a_modal';

let modal: Modal | null = null;

export default class extends Controller {
  connect() {
    modal = new Modal({
      autoOpen: false,
      updateURL: false,
      initLoad: false,
      maxWidth: '1200px',
      closeOnOverlay: true,
      closeButton: true,
      identifier: 'widget-list',
    });
  }

  openModal() {
    if (modal !== null) {
      modal.open('.js-widget-list-modal');
    }
  }
}
