import { Controller } from '@hotwired/stimulus';
import smoothscroll from 'smoothscroll-polyfill';

interface Section {
  offsetTop: number;
  offsetHeight: number;
}

export default class extends Controller {
  static targets = ['tocLink', 'block'];

  sections: Record<string, Section> = {};
  scrollPosition = 0;
  isScrolling: number | null = null;
  hasEventOnScroll = true;

  declare blockTargets: HTMLElement[];
  declare tocLinkTargets: HTMLElement[];

  connect() {
    smoothscroll.polyfill();


    this.blockTargets.forEach((element) => {
      if (!element.dataset.menuLink) {
        return;
      }

      this.sections[element.dataset.menuLink] = {
        offsetTop: element.offsetTop,
        offsetHeight: element.offsetHeight,
      };
    });

    this.onScroll = this.onScroll.bind(this);
    window.addEventListener('scroll', this.onScroll);

    this.goToBlock = this.goToBlock.bind(this);
    this.tocLinkTargets.forEach((link) => {
      link.addEventListener('click', this.goToBlock);
    });
  
  }

  disconnect() {
    window.removeEventListener('scroll', this.onScroll);
    this.tocLinkTargets.forEach((link) => {
      link.removeEventListener('click', this.goToBlock);
    })
  }

  removeActive() {
    this.tocLinkTargets.forEach((link) => { 
      link.classList.remove('is-active');
    });
  }

  makeActive(menuLink: HTMLElement) {
    this.removeActive();

    if (menuLink) {
      menuLink.classList.add('is-active');
    }
  }

  onScroll() {
    if (this.hasEventOnScroll) {
      const top = window.pageYOffset || document.documentElement.scrollTop;
      const isScrollDown = window.scrollY > this.scrollPosition;
      const listSections = isScrollDown ? Object.keys(this.sections) : Object.keys(this.sections).reverse();

      listSections.forEach((key) => {
        const { offsetTop, offsetHeight } = this.sections[key];

        const condition = isScrollDown
          ? offsetTop <= top
          : offsetTop + (offsetHeight / 2) > top;

        if (condition) {
          this.tocLinkTargets.forEach((link) => {
            if (link.dataset.menuLink === key) {
              this.makeActive(link);
            }
          })
        }
      });
    } else {
      // Control para cuando el scroll termina,
      // volver a habilitar los eventos en scroll que fueron deshabilitados cuando se hizo click en un link del menu
      if (this.isScrolling) {
        window.clearTimeout(this.isScrolling);
      }

      this.isScrolling = window.setTimeout(() => {
        this.hasEventOnScroll = true;
      }, 66);
    }

    this.scrollPosition = window.scrollY;
  }

  goToBlock(event: MouseEvent) {
    const { currentTarget } = event;
    if (!currentTarget) {
      return;
    }

    const { menuLink } = (currentTarget as HTMLElement).dataset;
    const gap = 80;

    this.blockTargets.forEach((block) => {
      if (block.dataset.menuLink === menuLink) {
        this.hasEventOnScroll = false;
        this.makeActive(currentTarget as HTMLElement);
        const { offsetTop } = block;
        window.scroll({ top: offsetTop + gap, behavior: 'smooth' });
      }
    });
  }
}
