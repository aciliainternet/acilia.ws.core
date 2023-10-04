import { init as initACropper, getCropperInstance, crop } from '../../modules/a_cropper';
import { show as showLoader, hide as hideLoader } from '../ws_loader';
import { hide as hideMessage, show as showMessage } from './ui_messages';
import { showError as showErrorNotification } from '../../modules/a_notifications';
import checkImagesSizes from './imageSizeValidator';

const cropperIgnoreClasses = ':not(.cropper-u-hidden):not(.cropper-hidden)';
const messageCropperPrefix = '.js-cropper-msg';
let modal = null;
let cancelEvent = null;

function getComponentConfig(elmId, ratio) {
  return {
    preview: document.querySelector(`[data-id="${elmId}"] .ws-cropper_preview`),
    aspectRatio: ratio,
    viewMode: 1,
    cropBoxResizable: true,
    rotatable: false,
    scalable: false,
    zoomable: false,
    background: false,
    zoomOnTouch: false,
    zoomOnWheel: false,
    wheelZoomRatio: false,
  };
}

function cancelCrop(event) {
  const dataId = event.currentTarget.dataset.id;
  document.getElementById(dataId).value = '';
  document.querySelector(`.ws-cropper_modal[data-id="${dataId}"] .ws-cropper_crop img`).src = '';
  document.querySelector(`.ws-cropper_modal[data-id="${dataId}"]`).dataset.croppIndex = 0;
  hideMessage(`${messageCropperPrefix}-${dataId}`);
  modal.close();
  if (cancelEvent) {
    cancelEvent();
  }
}

function saveCrop(id) {
  try {
    const fieldData = getCropperInstance(id);
    const hiddenFields = id.replace('asset', 'cropper_');
    fieldData.config.forEach((config) => {
      const idSelector = `${hiddenFields}${config.ratio.replace(':', 'x')}`;
      const value = `${config.data.width};${config.data.height};${config.data.x};${config.data.y}`;
      document.getElementById(idSelector).value = value;
      document.getElementById(`${id}_remove`).value = '';
    });

    document.querySelectorAll(`[data-id="${id.replace('_asset', '')}"]`).forEach((elm) => {
      elm.classList.remove('u-hidden');
    });

    if (document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${id}"] i`)) {
      document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${id}"]`)
        .classList.add('u-hidden');
    }

    document.querySelectorAll(`[data-id="${id.replace('_asset', '')}"]`).forEach((elm) => {
      const element = elm;
      if (element.querySelector('img')) {
        element.querySelector('img').src = fieldData.cropper.getCroppedCanvas().toDataURL();
      } else if (element.querySelector('.c-img-upload__wrapper-img')) {
        element.querySelector('.c-img-upload__wrapper-img').insertAdjacentHTML(
          'afterbegin',
          `<img class="c-img-upload__img" src="${fieldData.cropper.getCroppedCanvas().toDataURL()}">`,
        );
        element.querySelector('.c-img-upload__wrapper-img').classList.remove('u-hidden');
      }
    });
    document.querySelector(`.ws-cropper_modal[data-id="${id}"] .ws-cropper_crop img`).src = '';
    modal.close();
  } catch (error) {
    // this catch is to catch the error
    // 'InternalError: "too much recursion"' from the cropper library
  }
}

function setPreview(elementId, src) {
  const selector = `.ws-cropper_modal[data-id="${elementId}"] .ws-cropper_crop img`;
  document.querySelector(selector).src = src;
}

function checkCropSize(event) {
  try {
    const parent = event.currentTarget.closest('.ws-cropper_modal');
    // get the current index
    const index = parseInt(parent.dataset.croppIndex, 10) - 1;
    const { minimums } = getCropperInstance(parent.dataset.id).config[index];
    const { width, height } = event.detail;

    if (width < minimums.width || height < minimums.height) {
      getCropperInstance(parent.dataset.id).cropper.setData({
        width: Math.max(minimums.width, width),
        height: Math.max(minimums.height, height),
      });
    }
  } catch (error) {
    // this catch is to catch the error
    // 'InternalError: "too much recursion"' from the cropper library
  }
}

function showCropper(elm, cropperIndex) {
  try {
    const fieldData = getCropperInstance(elm.id);
    const croppersConfig = fieldData.config;
    const cropperConfig = croppersConfig[cropperIndex];
    const imageSelector = `.ws-cropper_modal[data-id="${elm.id}"] img${cropperIgnoreClasses}`;
    const cropperSelector = `.ws-cropper_modal[data-id="${elm.id}"]`;
    const config = getComponentConfig(elm.id, cropperConfig.ratioValue);

    document.querySelector(`${cropperSelector} .ws-cropper_crop`).classList.add('u-hidden');
    if (fieldData.cropper !== null) {
      document.querySelector(imageSelector).removeEventListener('crop', checkCropSize);
      fieldData.cropper.destroy();
    }

    const ratio = cropperConfig.ratio.replace('_', ':');
    const image = document.querySelector(imageSelector);
    fieldData.cropper = crop(image, config);

    image.addEventListener('crop', checkCropSize);
    document.querySelector(`${cropperSelector} .ws-cropper_crop`).classList.remove('u-hidden');
    document.querySelector(`${cropperSelector} .ws-cropper_details_ratio`).innerText = ratio;
    document.querySelector(`${cropperSelector} .ws-cropper_details_min_w`).innerText = cropperConfig.minimums.width;
    document.querySelector(`${cropperSelector} .ws-cropper_details_min_h`).innerText = cropperConfig.minimums.height;

    if (croppersConfig.length > (cropperIndex + 1)) {
      document.querySelector(`${cropperSelector} .ws-cropper_save`).style.display = 'none';
      document.querySelector(`${cropperSelector} .ws-cropper_next`).style.display = 'inline-block';
    } else {
      document.querySelector(`${cropperSelector} .ws-cropper_save`).style.display = 'inline-block';
      document.querySelector(`${cropperSelector} .ws-cropper_next`).style.display = 'none';
    }

    hideLoader();
  } catch (error) {
    // this catch is to catch the error
    // 'InternalError: "too much recursion"' from the cropper library
  }
}

function nextCrop(event) {
  const { id } = event.currentTarget.dataset;
  const index = parseInt(document.querySelector(`.ws-cropper_modal[data-id="${id}"]`).dataset.croppIndex, 10);

  const fieldData = getCropperInstance(id);
  const cropperConfig = fieldData.config[index - 1];
  cropperConfig.data = fieldData.cropper.getData();

  document.querySelector(`.ws-cropper_modal[data-id="${id}"]`).dataset.croppIndex = index + 1;

  if (event.currentTarget.classList.contains('ws-cropper_next')) {
    showCropper({ id }, index);
  } else if (event.currentTarget.classList.contains('ws-cropper_save')) {
    saveCrop(id);
  }
}

async function initCropper(event, loaderContainer) {
  const id = event.currentTarget.id || event.currentTarget.dataset.id;
  const { currentTarget } = event;
  const elm = document.querySelector(`#${id}[data-component="ws_cropper"]`);
  const modalCroppper = document.querySelector(`.ws-cropper_modal[data-id="${elm.id}"]`);
  let imageSrc = '';

  if (event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
    // if file is from drag and drop, we assign it to the elm object, to use the same logic.
    elm.files = event.dataTransfer.files;
  }

  if (elm.files && elm.files.length) {
    imageSrc = window.URL.createObjectURL(elm.files[0]);
  } else if (currentTarget.dataset.imageUrl) {
    imageSrc = currentTarget.dataset.imageUrl;
  }

  showLoader(loaderContainer);

  try {
    const imgValidator = await checkImagesSizes(imageSrc, JSON.parse(elm.dataset.minimums));

    if (!imgValidator.isValid) {
      const { error } = window.cmsTranslations.ws_cms_components.cropper;
      if (error) {
        const errorMsg = error.replace('%width%', imgValidator.minWidth).replace('%height%', imgValidator.minHeight);
        elm.value = '';
        showMessage(`${messageCropperPrefix}-${id}`, errorMsg, 'warning');
      }

      hideLoader();
    } else {
      setPreview(elm.id, imageSrc);
      if (currentTarget.dataset.imageUrl) {
        document.getElementById(`${id}_data`).value = currentTarget.dataset.imageId;
      }

      initACropper(elm);

      modalCroppper.querySelector('.ws-cropper_details_obs').innerText = '';
      modalCroppper.dataset.croppIndex = 1;

      document.querySelectorAll(`.ws-cropper_confirm[data-id="${elm.id}"]`).forEach(
        (input) => input.classList.remove('u-hidden'),
      );

      showCropper(elm, 0);

      if (elm.dataset.displayMode === 'list') {
        modal.refresh(`.ws-cropper_modal[data-id="${elm.id}"]`);
      } else {
        modal.open(`.ws-cropper_modal[data-id="${elm.id}"]`);
      }
    }
  } catch (err) {
    hideLoader();
    showErrorNotification(err.message);
  }
}

function init(assetElement, modalElement, closeEvent) {
  const { cmsTranslations } = window;
  if (cmsTranslations === undefined || cmsTranslations === null) {
    throw Error('No CMS Translations defined.');
  }

  cancelEvent = closeEvent;
  modal = modalElement;
  assetElement.addEventListener('change', initCropper);
  hideMessage(`${messageCropperPrefix}-${assetElement.id}`);

  document.querySelectorAll(`[data-id="${assetElement.id}"] .ws-cropper_container .ws-cropper_cancel`).forEach((elm) => {
    elm.addEventListener('click', cancelCrop);
  });

  document.querySelectorAll(`[data-id="${assetElement.id}"] .ws-cropper_container .ws-cropper_confirm`).forEach(
    (elm) => {
      elm.addEventListener('click', nextCrop);
    },
  );
}

export {
  init,
  initCropper,
};
