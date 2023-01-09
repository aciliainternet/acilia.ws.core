import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['button', 'input'];

  declare buttonTargets: HTMLElement[];
  declare inputTargets: HTMLInputElement[];
  
  handleToggle(event: MouseEvent) {
    const target = event.target as HTMLElement;
    
    if (!target) {
      return;
    }

    this.removeToggled();

    for (let i = 0; i < this.buttonTargets.length; i++) {
      if (this.buttonTargets[i] === target) {
        this.buttonTargets[i].classList.add('is-active');

        if (this.inputTargets[i]) {
          this.inputTargets[i].checked = true;
        }
        return;
      }
    }
  }

  removeToggled() {
    this.buttonTargets.forEach((btn) => { btn.classList.remove('is-active'); });
    this.inputTargets.forEach((input) => { input.checked = false; });
  }
}
