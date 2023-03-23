export class Loader {
  elementBehind: HTMLElement | null = null;

  constructor(element?: HTMLElement) {
    if (element) {
      this.elementBehind = element;
      this.elementBehind = this.elementBehind.querySelector('.js-loader')
        ? this.elementBehind 
        : this.elementBehind.parentElement;
    }
  }

  show() {
    if (this.elementBehind !== null) {
      this.elementBehind.querySelector('.js-loader')?.classList.add('is-active');
      this.elementBehind.classList.add('no-scroll');
    }
  }

  hide() {
    if (this.elementBehind !== null) {
      this.elementBehind.querySelector('.js-loader')?.classList.remove('is-active');
      this.elementBehind.classList.remove('no-scroll');
    }
  }
}
