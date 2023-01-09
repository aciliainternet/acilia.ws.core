import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    rowId: String,
  };

  declare rowIdValue: string;

  toggleRow() {
    this.element.classList.toggle('is-open');
    
    const panel = document.querySelector(`#${this.rowIdValue}`);

    if (panel) {
      panel.classList.toggle('is-active');
    }
  }
}
