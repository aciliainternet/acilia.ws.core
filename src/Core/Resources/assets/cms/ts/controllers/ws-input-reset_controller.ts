import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.element.addEventListener('click', this.reset);
    if (this.element.parentElement) {
      this.element.parentElement.querySelector('input')?.addEventListener('input', this.change);
    }
  }

  reset(event: Event) {
    const resetButton = event.currentTarget as HTMLButtonElement;
    const parent = resetButton.parentElement;
    if (parent) {
      const input = parent.querySelector('input');
      if (input) {
        input.value = '';
        parent.classList.remove('has-content');
      }
    }
  }

  change(event: Event) {
    const input = event.currentTarget as HTMLInputElement;
    const parent = input.parentElement;

    if (parent) {
      if (parent.classList.contains('has-content')) {
        if (input.value.length < 1) {
          parent.classList.remove('has-content');
        }
      } else {
        parent.classList.add('has-content');
      }
    }
  }
}
