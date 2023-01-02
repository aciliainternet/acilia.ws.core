import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.element.addEventListener('keyup', this.sanitizeInput);
    this.element.addEventListener('keydown', this.validateInput);
  }

  validateInput(event: Event) {
    const regex = new RegExp('^[A-Za-z0-9 _-]*$');

    if (!regex.test((event as KeyboardEvent).key)) {
      event.preventDefault();
    }
  }

  sanitizeInput(event: Event) {
    const current = event.currentTarget as HTMLInputElement;

    if (!current) {
      return;
    }

    current.value = current.value.toLowerCase();
    current.value = current.value.replace(/\s+/g, '-');
    current.value = current.value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }
}
