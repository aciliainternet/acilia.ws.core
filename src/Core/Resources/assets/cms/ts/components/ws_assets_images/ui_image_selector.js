import './ws_image_selector/search';
import './ws_image_selector/dragDrop';
import { init as initImageList } from './ws_image_selector/imageList';
import { init as initCropper } from './ui_cropper';

function newFile(event) {
  const { id } = event.currentTarget.closest('.js-image-selector-modal').dataset;
  document.getElementById(id).click();
}

function init(assetImageElement, modal) {
  if (assetImageElement.id !== undefined) {
    const dataString = `[data-id="${assetImageElement.id}"]`;
    initCropper(assetImageElement, modal, () => {
      initImageList(assetImageElement.id);
      modal.open(`.js-image-selector-modal${dataString}`);
    });
    initImageList(assetImageElement.id);

    document.querySelector(`.js-img-selector-new${dataString}`).addEventListener('click', newFile);
    modal.open(`.js-image-selector-modal${dataString}`);
  }
}

export default init;
