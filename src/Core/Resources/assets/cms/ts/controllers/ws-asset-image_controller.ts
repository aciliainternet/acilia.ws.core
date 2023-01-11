import { Controller } from '@hotwired/stimulus';
import initModalImageSelector from '../../js/components/ws_assets_images/ui_image_selector';
import { init as initCropper } from '../../js/components/ws_assets_images/ui_cropper';
import Modal from '../modules/a_modal';

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

    // document.querySelectorAll('.js-open-modal').forEach((element) => {
    //   element.addEventListener('click', handleBehaviour);
    // });

    // document.querySelectorAll('.js-delete-asset-image').forEach((element) => {
    //   element.addEventListener('click', deleteAssetImage);
    // });
  }

  deleteAssetImage() {
    this.removeFieldTarget.value = 'remove'
    if (this.hasImageTarget) {
      this.imageTarget.classList.add('u-hidden');
    }
    this.placeholderTarget.classList.remove('u-hidden');
    this.changeImageTarget.classList.add('u-hidden');
    this.uploadNewImageTarget.classList.remove('u-hidden');
    // document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${idAssetComponent}_asset"]`)
    //    .classList.remove('u-hidden');
  }
}

const modal = new Modal({
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

function handleBehaviour(event) {
  const assetImageElement = document.querySelector(
    `#${event.currentTarget.dataset.idAssetComponent}[data-component="ws_cropper"]`,
  );
  if (assetImageElement.dataset.displayMode === 'list'
    && document.querySelector('.js-image-selector-modal').offsetWidth === 0
    && document.querySelector('.js-image-selector-modal').offsetHeight === 0) {
    event.preventDefault();
    initModalImageSelector(assetImageElement, modal);
  } else if (assetImageElement.dataset.displayMode === 'crop') {
    initCropper(assetImageElement, modal);
    assetImageElement.click();
  }
}
