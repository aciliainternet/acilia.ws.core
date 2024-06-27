import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['toggle'];

  declare toggleTarget: HTMLElement;

  connect() {
    this.hideDropdown = this.hideDropdown.bind(this);
    document.body.addEventListener('click', this.hideDropdown);
  }

  hideDropdown() {
    this.element.classList.remove('show');
    this.toggleTarget.classList.remove('show');
  }

  toggleDropdown(event: MouseEvent) {
    event.stopPropagation();
    const isOpen = this.toggleTarget.classList.contains('show');

    // We need to hide all dropdowns in the page
    this.hideAllDropDowns();

    if (!isOpen) {
      this.element.classList.add('show');
      this.toggleTarget.classList.add('show');
    }
  }

  hideAllDropDowns() {
    document
      .querySelectorAll<HTMLElement>('[data-controller="ws-dropdown"]')
      .forEach((element) => {
        element.classList.remove('show');
      });

    document
      .querySelectorAll<HTMLElement>('[data-ws-dropdown-target="toggle"')
      .forEach((element) => {
        element.classList.remove('show');
      });
  }
}
