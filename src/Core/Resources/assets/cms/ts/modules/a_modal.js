/*
* a_modal.js v1.2.5
* https://github.com/aciliainternet/CN-JsUtil/blob/master/modules/a_modal
*
* Created by BrunoViera for AciliaInternet
*/
class AModal {
  constructor(options = {}) {
    if (!options.identifier) {
      throw new Error('Missing identifier, can\'t create modal');
    }

    const modalId = 'a-modal';
    const containerId = 'a-container';
    const closeButtonClass = 'a-close fal fa-times';
    const closeButtonId = 'a-close';
    this.contentClass = 'a-content';
    this.overlayClass = 'a-overlay';
    this.openClass = 'a-open';
    this.openAnimation = 'fade-and-drop';
    this.options = {};

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

    // eslint-disable-next-line @typescript-eslint/no-this-alias
    const self = this;

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
      const openModal = (event) => {
        self.open(event.target.dataset.modal);
      };
      const buttons = document.getElementsByClassName('a-m-trigger');
      for (let i = buttons.length - 1; i >= 0; i--) {
        const elm = buttons[i];
        if (elm.dataset.modal !== undefined && elm.dataset.modal.charAt(0) === '#') {
          elm.addEventListener('click', openModal, false);
        }
      }
    }

    this.options.modalClass = options.modalClass ? options.modalClass : false;
    this.options.onOpen = options.onOpen ? options.onOpen : undefined;
    this.options.onRefresh = options.onRefresh ? options.onRefresh : undefined;
    this.options.onClose = options.onClose ? options.onClose : undefined;
    if (options.closeOnOverlay !== undefined) {
      this.options.closeOnOverlay = options.closeOnOverlay;
    }

    this.container = document.querySelector(`#${containerId}[data-id='${options.identifier}']`);

    const closeButtonElement = this.container.querySelector(`#${closeButtonId}`);
    if (options.closeButton === false) {
      this.options.closeButton = false;
      closeButtonElement.style.display = 'none';
    } else {
      if (options.closeClass !== undefined) {
        closeButtonElement.classList.add(options.closeClass);
      }
      closeButtonElement.addEventListener('click', () => {
        self.close();
      }, false);
    }

    this.container.style.maxWidth = this.options.maxWidth;
    this.container.style.minWidth = this.options.minWidth;

    // add close when click on overlay
    if (this.options.closeOnOverlay) {
      document.getElementsByClassName(this.overlayClass)[0].addEventListener('click', (event) => {
        if (event.target.classList.contains(self.overlayClass)) {
          self.close();
        }
      }, false);
    }

    if (options.initLoad && document.location.hash.length) {
      document.querySelector(`.a-m-trigger[data-modal="${document.location.hash}"]`);
      this.open(document.location.hash);
    }
  }

  open(contentSelector) {
    try {
      const content = document.querySelector(contentSelector);
      const hash = contentSelector.substring(1);
      if (content) {
        if (this.options.updateURL) {
          document.location.hash = hash;
        }
        content.style.display = 'block';
        this.container.getElementsByClassName(this.contentClass)[0].appendChild(content);
        this.modal.classList.add(this.openClass, this.openAnimation);
        if (this.options.modalClass) {
          this.modal.classList.add(this.options.modalClass);
        }
        this.container.classList.add(this.openClass, this.openAnimation);
        // prevent body scroll
        document.body.style.overflow = 'hidden';

        // excecute callback if we have one seted
        if (this.options.onOpen) {
          this.options.onOpen();
        }
      }
    } catch (e) {
      throw new Error(`AModal fails when try to open, ${e} `);
    }
  }

  close() {
    try {
      // document.location.hash = '';
      this.modal.classList.remove(this.openClass, this.openAnimation);
      if (this.options.modalClass) {
        this.modal.classList.remove(this.options.modalClass);
      }

      this.container.classList.remove(this.openClass, this.openAnimation);
      const content = this.container.querySelector(`.${this.contentClass} > div`);
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

  refresh(contentSelector) {
    try {
      const newContent = document.querySelector(contentSelector);
      const hash = contentSelector.substring(1);
      if (newContent) {
        // get old content an remove it from modal
        const oldContent = this.container.querySelector(`.${this.contentClass} > div`);
        oldContent.style.display = 'none';
        document.body.appendChild(oldContent);

        // set values for new content and add it onto the modal
        if (this.options.updateURL) {
          document.location.hash = hash;
        }
        newContent.style.display = 'block';
        this.container.getElementsByClassName(this.contentClass)[0].appendChild(newContent);

        // excecute callback if we have one seted
        if (this.options.onRefresh) {
          this.options.onRefresh();
        }
      }
    } catch (e) {
      throw new Error(`AModal fails when try to refresh, ${e} `);
    }
  }
}

// eslint-disable-next-line no-undef
module.exports = AModal;
