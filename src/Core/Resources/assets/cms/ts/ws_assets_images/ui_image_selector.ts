import './ws_image_selector/search';
import './ws_image_selector/dragDrop';
import { init as initImageList } from './ws_image_selector/imageList';
import { init as initCropper } from './ui_cropper';
import AModal from '../modules/a_modal';

function newFile(event: MouseEvent) {
  const currentTarget = event.currentTarget as HTMLElement;
  const id = currentTarget.closest<HTMLElement>('.js-image-selector-modal')
    ?.dataset.id;

  if (id) {
    document.getElementById(id)?.click();
  }
}

function init(assetImageElement: HTMLImageElement, modal: AModal) {
  if (assetImageElement.id !== undefined) {
    const dataString = `[data-id="${assetImageElement.id}"]`;

    initCropper(assetImageElement, modal, () => {
      initImageList(assetImageElement.id);
      modal.open(`.js-image-selector-modal${dataString}`);
    });

    initImageList(assetImageElement.id);

    document
      .querySelector<HTMLImageElement>(`.js-img-selector-new${dataString}`)
      ?.addEventListener('click', newFile);

    modal.open(`.js-image-selector-modal${dataString}`);
  }
}

export default init;
