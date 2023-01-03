import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['tab', 'tabPanel'];

    declare tabTargets: HTMLElement[];
    declare tabPanelTargets: HTMLElement[];

    connect() {
        this.enableTab = this.enableTab.bind(this);
        this.disableTab = this.disableTab.bind(this);
        
        this.element.addEventListener('tab:enable', this.enableTab as EventListener);
        this.element.addEventListener('tab:disable', this.disableTab as EventListener);
    }

    onTabClick(event: MouseEvent) {
        const target = event.target as HTMLElement;

        if (!target || target.classList.contains('is-disabled')) {
            return;
        }

        let newTabIndex = 0;
        this.tabTargets.forEach((tabTarget, index) => {
            tabTarget.classList.remove('is-active');
            if (tabTarget === target) {
                tabTarget.classList.add('is-active');
                newTabIndex = index;
            }
        });

        this.tabPanelTargets.forEach((tabPanelTarget, index) => {
            tabPanelTarget.classList.remove('is-active');
            if (index === newTabIndex) {
                tabPanelTarget.classList.add('is-active');
            }
        });

        this.element.dispatchEvent(new CustomEvent('tab:changed', { bubbles: true }));
    }

    enableTab(event: CustomEvent<{ index: number }>) {
        const target = this.tabTargets[event.detail.index];
        if (!target) {
            return;
        }

        target.classList.remove('is-disabled');
    }

    disableTab(event: CustomEvent<{ index: number}>) {
        const target = this.tabTargets[event.detail.index];
        if (!target) {
            return;
        }

        target.classList.add('is-disabled');
    }
}
