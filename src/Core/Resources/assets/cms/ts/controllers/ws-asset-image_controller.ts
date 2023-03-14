import { Controller } from '@hotwired/stimulus';
import initModalImageSelector from '../../js/components/ws_assets_images/ui_image_selector';
import { init as initCropper } from '../../js/components/ws_assets_images/ui_cropper';
import Modal from '../modules/a_modal';

let modal: Modal | null = null;

export default class extends Controller {
  static targets = ['image', 'placeholder', 'uploadNewImage', 'changeImage', 'removeField'];

  declare hasImageTarget: boolean;
  declare imageTarget: HTMLImageElement;
  declare placeholderTarget: HTMLElement;
  declare changeImageTarget: HTMLElement;
  declare uploadNewImageTarget: HTMLElement;
  declare removeFieldTarget: HTMLInputElement;

  connect() {
    const { cmsTranslations } = window;

    if (cmsTranslations === undefined || cmsTranslations === null) {
      throw Error('No CMS Translations defined.');
    }

    if (modal === null) {
      modal = new Modal({
        autoOpen: false,
        updateURL: false,
        initLoad: false,
        maxWidth: '1200px',
        closeOnOverlay: true,
        closeButton: true,
        identifier: 'image-selector',
        onClose: () => {
          const searchInput = document.querySelector<HTMLInputElement>('.js-search-input');
          if (searchInput) {
            searchInput.value = '';
          }
        },
      });
    }
  }

  deleteAssetImage() {
    this.removeFieldTarget.value = 'remove'
    if (this.hasImageTarget) {
      this.imageTarget.classList.add('u-hidden');
    }
    this.placeholderTarget.classList.remove('u-hidden');
    this.changeImageTarget.classList.add('u-hidden');
    this.uploadNewImageTarget.classList.remove('u-hidden');
  }

  openModal(event: MouseEvent & { currentTarget: HTMLElement }) {
    const assetImageElement = document.querySelector<HTMLElement>(
      `#${event.currentTarget.dataset.idAssetComponent}[data-component="ws_cropper"]`,
    );
  
    if (!assetImageElement) {
      return;
    }

    if (assetImageElement.dataset.displayMode === 'list'
      && document.querySelector<HTMLElement>('.js-image-selector-modal')?.offsetWidth === 0
      && document.querySelector<HTMLElement>('.js-image-selector-modal')?.offsetHeight === 0) {
      event.preventDefault();
      initModalImageSelector(assetImageElement, modal);
    } else if (assetImageElement.dataset.displayMode === 'crop') {
      initCropper(assetImageElement, modal);
      assetImageElement.click();
    }
  }
}
