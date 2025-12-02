import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  declare private regex: string|RegExp
  declare regexValue: string;

  static values = {
    regex: String
  }

  connect() {
    this.element.addEventListener('keyup', this.sanitizeInput);
    this.element.addEventListener('keydown', this.validateInput);

    this.regex = this.regexValue || '^[A-Za-z0-9 _-]*$';
  }

  validateInput(event: Event) {
    const regex = new RegExp(this.regex);

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
    current.value = current.value
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');
  }
}
