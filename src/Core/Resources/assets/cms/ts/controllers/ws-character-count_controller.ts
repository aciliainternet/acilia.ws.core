import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['field'];
  static values = {
    max: Number,
  }
  declare fieldTarget: HTMLInputElement | HTMLTextAreaElement;
  declare fieldTargets: HTMLElement[];
  declare maxValue: number;

  connect() {
    this.change()
  }

  change() {
    const text = this.element.querySelector('small.help-text') as HTMLElement;

    const countSpan = text.querySelector('.js-count');
    if (countSpan) {
      countSpan.textContent = this.fieldTarget.value.length.toString();
    }

    const maxCountSpan = text.querySelector('.js-maxCount');
    if (maxCountSpan) {
      maxCountSpan.textContent = this.maxValue.toString();
    }

    if (this.fieldTarget.value.length > this.maxValue) {
      text.classList.add('u-text-danger')
    } else {
      text.classList.remove('u-text-danger');
    }
  }
}
