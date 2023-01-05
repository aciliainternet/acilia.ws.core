import { Controller } from '@hotwired/stimulus';
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

export default class extends Controller {
  openModal() {
    modal.open('.js-widget-list-modal');
  }
}
