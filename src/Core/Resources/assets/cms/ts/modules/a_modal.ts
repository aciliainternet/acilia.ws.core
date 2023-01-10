export interface ModalOptions {
  identifier: string;
  updateURL: boolean;
  closeButton: boolean;
  autoOpen: boolean;
  closeOnOverlay: boolean;
  maxWidth: string;
  minWidth: string;
  modalClass: string | false;
  closeClass: string;
  onOpen: Function;
  onRefresh: Function;
  onClose: Function;
  initLoad: boolean;
}

/*
* a_modal.js v1.2.5
* https://github.com/aciliainternet/CN-JsUtil/blob/master/modules/a_modal
*
* Created by BrunoViera for AciliaInternet
*/
export default class AModal {
  contentClass = 'a-content';
  overlayClass = 'a-overlay';
  openClass = 'a-open';
  openAnimation = 'fade-and-drop';
  options: Partial<ModalOptions> = {};
  modal: HTMLElement | null;
  container: HTMLElement | null;

  constructor(options: Partial<ModalOptions> = {}) {
    if (!options.identifier) {
      throw new Error('Missing identifier, can\'t create modal');
    }

    const modalId = 'a-modal';
    const containerId = 'a-container';
    const closeButtonClass = 'a-close fal fa-times';
    const closeButtonId = 'a-close';

    // prepare modal frame
    this.modal = document.getElementById(modalId);
    this.modal = document.createElement('div');
    this.modal.className = this.overlayClass;
    this.modal.setAttribute('id', modalId);

    const containerDiv = document.createElement('div');
    containerDiv.setAttribute('id', containerId);
    containerDiv.setAttribute('data-id', options.identifier);

    const closeButton = document.createElement('button');
    closeButton.setAttribute('id', closeButtonId);
    closeButton.className = closeButtonClass;

    const contentDiv = document.createElement('div');
    contentDiv.className = this.contentClass;

    containerDiv.appendChild(contentDiv);
    containerDiv.appendChild(closeButton);
    this.modal.appendChild(containerDiv);
    document.body.appendChild(this.modal);

    this.options = {
      updateURL: true,
      closeButton: true,
      autoOpen: false,
      closeOnOverlay: true,
      maxWidth: '830px',
      minWidth: '280px',
    };

    if (options.updateURL !== undefined) {
      this.options.updateURL = options.updateURL;
    }

    if (options.maxWidth !== undefined) {
      this.options.maxWidth = options.maxWidth;
    }

    if (options.minWidth !== undefined) {
      this.options.minWidth = options.minWidth;
    }

    if (options.autoOpen) {
      this.options.autoOpen = true;

      const openModal = (event: MouseEvent) => {
        const target = event.target as HTMLElement;
        if (target.dataset.modal) {
          this.open(target.dataset.modal);
        }
      };

      const buttons = document.querySelectorAll<HTMLButtonElement>('.a-m-trigger');
      
      buttons.forEach((elm) => {
         if (elm.dataset.modal !== undefined && elm.dataset.modal.charAt(0) === '#') {
          elm.addEventListener('click', openModal, false);
        }
      });
    }

    this.options.modalClass = options.modalClass ? options.modalClass : false;
    this.options.onOpen = options.onOpen ? options.onOpen : undefined;
    this.options.onRefresh = options.onRefresh ? options.onRefresh : undefined;
    this.options.onClose = options.onClose ? options.onClose : undefined;

    if (options.closeOnOverlay !== undefined) {
      this.options.closeOnOverlay = options.closeOnOverlay;
    }

    this.container = document.querySelector(`#${containerId}[data-id='${options.identifier}']`);

    const closeButtonElement = this.container?.querySelector<HTMLElement>(`#${closeButtonId}`);
    if (options.closeButton === false) {
      this.options.closeButton = false;
      if (closeButtonElement) {
        closeButtonElement.style.display = 'none';
      }
    } else {
      if (closeButtonElement) {
        if (options.closeClass !== undefined) {
        
          closeButtonElement.classList.add(options.closeClass);
        
        }
      
        closeButtonElement.addEventListener('click', () => {
          this.close();
        }, false);
      }
    }

    if (this.container !== null) {
      if (this.options.maxWidth) {
        this.container.style.maxWidth = this.options.maxWidth;
      }
      if (this.options.minWidth) {
        this.container.style.minWidth = this.options.minWidth;
      }
    }

    // add close when click on overlay
    if (this.options.closeOnOverlay) {
      document.getElementsByClassName(this.overlayClass)[0].addEventListener('click', (event) => {
        const target = event.target as HTMLElement;
        if (target.classList.contains(this.overlayClass)) {
          this.close();
        }
      }, false);
    }

    if (options.initLoad && document.location.hash.length) {
      document.querySelector(`.a-m-trigger[data-modal="${document.location.hash}"]`);
      this.open(document.location.hash);
    }
  }

  open(contentSelector: string) {
    try {
      const content = document.querySelector<HTMLElement>(contentSelector);
      const hash = contentSelector.substring(1);
      if (content) {
        if (this.options.updateURL) {
          document.location.hash = hash;
        }

        content.style.display = 'block';

        if (this.container !== null) {
          this.container.getElementsByClassName(this.contentClass)[0].appendChild(content);
          this.container.classList.add(this.openClass, this.openAnimation);
        }

        if (this.modal !== null) {
          this.modal.classList.add(this.openClass, this.openAnimation);

          if (this.options.modalClass) {
            this.modal.classList.add(this.options.modalClass);
          }
        }
        
        // prevent body scroll
        document.body.style.overflow = 'hidden';

        // execute callback if we have one set
        if (this.options.onOpen) {
          this.options.onOpen();
        }
      }
    } catch (e) {
      throw new Error(`AModal fails when try to open, ${e} `);
    }
  }

  close() {
    if (this.modal === null || this.container === null) {
      return;
    }

    try {
      // document.location.hash = '';
      this.modal.classList.remove(this.openClass, this.openAnimation);
      if (this.options.modalClass) {
        this.modal.classList.remove(this.options.modalClass);
      }
    
      this.container.classList.remove(this.openClass, this.openAnimation);
      const content = this.container.querySelector<HTMLElement>(`.${this.contentClass} > div`);
      if (content) {
        content.style.display = 'none';
        document.body.appendChild(content);
      }

      this.container.getElementsByClassName(this.contentClass)[0].innerHTML = '';

      // prevent body scroll
      document.body.style.overflow = 'auto';

      // excecute callback if we have one seted
      if (this.options.onClose) {
        this.options.onClose();
      }
    } catch (e) {
      throw new Error(`AModal fails when try to close, ${e} `);
    }
  }

  refresh(contentSelector: string) {
    if (this.container === null) {
      return;
    }

    try {
      const newContent = document.querySelector<HTMLElement>(contentSelector);
      const hash = contentSelector.substring(1);
      if (newContent) {
        // get old content an remove it from modal
        const oldContent = this.container.querySelector<HTMLElement>(`.${this.contentClass} > div`);
        if (oldContent) {
          oldContent.style.display = 'none';
          document.body.appendChild(oldContent);
        }

        // set values for new content and add it onto the modal
        if (this.options.updateURL) {
          document.location.hash = hash;
        }

        newContent.style.display = 'block';
        this.container.getElementsByClassName(this.contentClass)[0].appendChild(newContent);

        // execute callback if we have one set
        if (this.options.onRefresh) {
          this.options.onRefresh();
        }
      }
    } catch (e) {
      throw new Error(`AModal fails when try to refresh, ${e} `);
    }
  }
}
