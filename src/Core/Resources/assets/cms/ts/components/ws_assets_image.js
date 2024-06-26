import initModalImageSelector from './ws_assets_images/ui_image_selector';
import { init as initCropper } from './ws_assets_images/ui_cropper';
import Modal from '../modules/a_modal';

const modal = new Modal({
  autoOpen: false,
  updateURL: false,
  initLoad: false,
  maxWidth: '1200px',
  closeOnOverlay: true,
  closeButton: true,
  identifier: 'image-selector',
  onClose: () => {
    document.querySelector('.js-search-input').value = '';
  },
});

function deleteAssetImage(event) {
  const { idAssetComponent } = event.currentTarget.dataset;

  if (idAssetComponent) {
    document.getElementById(`${idAssetComponent}_asset_remove`).value = 'remove';
    document.querySelector(`[data-id="${idAssetComponent}"]`).classList.add('u-hidden');
    document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${idAssetComponent}_asset"]`)
      .classList.remove('u-hidden');
  }
}


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

function init() {
  const { cmsTranslations } = window;

  if (cmsTranslations === undefined || cmsTranslations === null) {
    throw Error('No CMS Translations defined.');
  }

  document.querySelectorAll('.js-open-modal').forEach((element) => {
    element.addEventListener('click', handleBehaviour);
  });

  document.querySelectorAll('.js-delete-asset-image').forEach((element) => {
    element.addEventListener('click', deleteAssetImage);
  });
}

export default init;
