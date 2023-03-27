import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['sidebar'];

  declare sidebarTarget: HTMLElement;

  connect() {
    // if a subnav is active, we can't set the subnav class in the html using twig
    // then we have to open the subnav using js
    const linkActive = document.querySelector(
      'ul.c-sidebar__submenu.collapse a.c-sidebar__link.is-active'
    );

    if (linkActive) {
      const closestCollapsed = linkActive.closest('ul.collapse');

      if (closestCollapsed) {
        closestCollapsed.classList.remove('collapse');
      }
    }
  }

  menuAction(event: MouseEvent) {
    event.stopPropagation();
    const target = event.target as HTMLLIElement | null;
    if (target?.closest('.js-submenu-children')) {
      return true;
    }

    const currentTarget = event.currentTarget as HTMLElement;
    const submenu = currentTarget.querySelector('ul.c-sidebar__submenu');

    if (submenu) {
      submenu.classList.toggle('collapse');
      currentTarget.classList.toggle('is-open');
    }

    return true;
  }

  toggleSidebar(event: MouseEvent) {
    this.sidebarTarget.classList.toggle('is-visible');
    event.stopPropagation();
  }
}
