import { Controller } from '@hotwired/stimulus';
import { visit } from '@hotwired/turbo';

export default class extends Controller {
  connect() {
    this.element.addEventListener('click', (event) => {
      event.preventDefault();
      const href = this.element.getAttribute('href');

      if (href) {
        visit(href);
      }
    });
  }
}
