import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['filterRow'];

  declare filterRowTarget: HTMLElement;

  toggleFilter() {
    if(this.filterRowTarget.classList.contains('u-hidden')) {
      this.filterRowTarget.classList.remove('u-hidden');
    } else {
      this.filterRowTarget.classList.add('u-hidden');
    }
  } 
}
