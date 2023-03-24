import { init as initACropper, getCropperInstance, crop } from '../modules/a_cropper';
import { Loader } from '../tools/ws_loader';
import { hide as hideMessage, show as showMessage } from './ui_messages';
import { showError as showErrorNotification } from '../modules/a_notifications';
import checkImagesSizes from '../tools/imageSizeValidator';
import AModal from '../modules/a_modal';

const cropperIgnoreClasses = ':not(.cropper-u-hidden):not(.cropper-hidden)';
const messageCropperPrefix = '.js-cropper-msg';
let modal: AModal | null = null;
let cancelEvent: (() => void) | null = null;

function getComponentConfig(elmId: string, ratio: number): Cropper.Options {
  const preview = document.querySelector(`[data-id="${elmId}"] .ws-cropper_preview`);

  return {
    ...(preview && { preview }),
    aspectRatio: ratio,
    viewMode: 1,
    cropBoxResizable: true,
    rotatable: false,
    scalable: false,
    zoomable: false,
    background: false,
    zoomOnTouch: false,
    zoomOnWheel: false,
    wheelZoomRatio: 0,
  };
}

function cancelCrop(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement;
  const dataId = currentTarget.dataset.id;

  if (!dataId) {
    return;
  }

  const inputElement = document.querySelector<HTMLInputElement>(`#${dataId}`);
  if (inputElement) {
    inputElement.value = '';
  }
  
  const cropperModal = document.querySelector<HTMLElement>(`.ws-cropper_modal[data-id="${dataId}"]`);

  if (cropperModal) {
    cropperModal.dataset.croppIndex = (0).toString(10);

    const img = cropperModal.querySelector<HTMLImageElement>(`.ws-cropper_crop img`);
    if (img) {
      img.src = '';
    }
  }

  hideMessage(`${messageCropperPrefix}-${dataId}`);

  if (modal) {
    modal.close();
  }

  if (cancelEvent) {
    cancelEvent();
  }
}

function saveCrop(id: string) {
  try {
    const fieldData = getCropperInstance(id);
    const hiddenFields = id.replace('asset', 'cropper_');
    fieldData.config.forEach((config) => {
      const idSelector = `${hiddenFields}${config.ratio.replace(':', 'x')}`;
      const value = `${config.data?.width};${config.data?.height};${config.data?.x};${config.data?.y}`;

      const inputElement = document.querySelector<HTMLInputElement>(`#${idSelector}`);
      if (inputElement) {
        inputElement.value = value;
      }
      
      const inputElementRemove = document.querySelector<HTMLInputElement>(`#${id}_remove`);
      if (inputElementRemove) {
        inputElementRemove.value = '';
      }
    });

    document.querySelectorAll(`[data-id="${id.replace('_asset', '')}"]`).forEach((elm) => {
      elm.classList.remove('u-hidden');
    });

    const notOnPreview = document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${id}"] i`);
    if (notOnPreview) {
      notOnPreview.classList.add('u-hidden');
    }

    document.querySelectorAll(`[data-id="${id.replace('_asset', '')}"]`).forEach((element) => {
      const img = element.querySelector<HTMLImageElement>('img');
      const wrapperImg = element.querySelector('.c-img-upload__wrapper-img');
      if (img) {
        img.src = fieldData.cropper?.getCroppedCanvas().toDataURL() || '';
      } else if (wrapperImg) {
        wrapperImg.insertAdjacentHTML(
          'afterbegin',
          `<img class="c-img-upload__img" src="${fieldData.cropper?.getCroppedCanvas().toDataURL()}">`,
        );

        element.querySelector('.c-img-upload__wrapper-img')?.classList.remove('u-hidden');
      }
    });

    const img = document.querySelector<HTMLImageElement>(`.ws-cropper_modal[data-id="${id}"] .ws-cropper_crop img`);
    if (img) {
      img.src = '';
    }

    if (modal) {
      modal.close();
    }
  } catch (error) {
    // this catch is to catch the error
    // 'InternalError: "too much recursion"' from the cropper library
  }
}

function setPreview(elementId: string, src: string) {
  const selector = `.ws-cropper_modal[data-id="${elementId}"] .ws-cropper_crop img`;
  const img = document.querySelector<HTMLImageElement>(selector);

  if (img) {
    img.src = src;
  }
}

function checkCropSize(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement;
  try {
    const parent = currentTarget.closest<HTMLElement>('.ws-cropper_modal');
    const croppIndex = parent?.dataset.croppIndex;
    const id = parent?.dataset.id;

    if (!croppIndex || !id) {
      return;
    }

    // get the current index
    const index = parseInt(croppIndex, 10) - 1;
    const { minimums } = getCropperInstance(id).config[index];
    const { width, height } = (event as CustomEvent).detail as { width: number; height: number };

    if (width < minimums.width || height < minimums.height) {
      getCropperInstance(id).cropper?.setData({
        width: Math.max(minimums.width, width),
        height: Math.max(minimums.height, height),
      });
    }
  } catch (error) {
    // this catch is to catch the error
    // 'InternalError: "too much recursion"' from the cropper library
  }
}

function showCropper(elm: { id: string }, cropperIndex: number) {
  try {
    const fieldData = getCropperInstance(elm.id);
    const croppersConfig = fieldData.config;
    const cropperConfig = croppersConfig[cropperIndex];
    const imageSelector = `.ws-cropper_modal[data-id="${elm.id}"] img${cropperIgnoreClasses}`;
    const cropperSelector = `.ws-cropper_modal[data-id="${elm.id}"]`;
    const config = getComponentConfig(elm.id, Number(cropperConfig.ratioValue));

    document.querySelector(`${cropperSelector} .ws-cropper_crop`)?.classList.add('u-hidden');

    if (fieldData.cropper !== null) {
      document.querySelector(imageSelector)?.removeEventListener('crop', checkCropSize);
      fieldData.cropper.destroy();
    }

    const ratio = cropperConfig.ratio.replace('_', ':');
    const image = document.querySelector<HTMLImageElement>(imageSelector);
    if (image) {
      fieldData.cropper = crop(image, config);
      image.addEventListener('crop', checkCropSize);
    }
    
    document.querySelector(`${cropperSelector} .ws-cropper_crop`)?.classList.remove('u-hidden');

    const ratioElem = document.querySelector<HTMLElement>(`${cropperSelector} .ws-cropper_details_ratio`);
    const wElem = document.querySelector<HTMLElement>(`${cropperSelector} .ws-cropper_details_min_w`);
    const hElem = document.querySelector<HTMLElement>(`${cropperSelector} .ws-cropper_details_min_h`);

    if (ratioElem && wElem && hElem) {
      ratioElem.innerText = ratio;
      wElem.innerText = cropperConfig.minimums.width;
      hElem.innerText = cropperConfig.minimums.height;
    }

    const saveElem = document.querySelector<HTMLElement>(`${cropperSelector} .ws-cropper_save`);
    const nextElem = document.querySelector<HTMLElement>(`${cropperSelector} .ws-cropper_next`);

    if (saveElem && nextElem) {
      if (croppersConfig.length > (cropperIndex + 1)) {
        saveElem.style.display = 'none';
        nextElem.style.display = 'inline-block';
      } else {
        saveElem.style.display = 'inline-block';
        nextElem.style.display = 'none';
      }
    }

    Loader.hide();
  } catch (error) {
    // this catch is to catch the error
    // 'InternalError: "too much recursion"' from the cropper library
  }
}

function nextCrop(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement;
  const id = currentTarget.dataset.id;

  if (!id) {
    return;
  }

  const cropperElem = document.querySelector<HTMLElement>(`.ws-cropper_modal[data-id="${id}"]`);
  if (!cropperElem) {
    return;
  }

  const index = parseInt(cropperElem.dataset.croppIndex || '0', 10);

  const fieldData = getCropperInstance(id);
  const cropperConfig = fieldData.config[index - 1];
  cropperConfig.data = fieldData.cropper?.getData();

  cropperElem.dataset.croppIndex = (index + 1).toString(10);

  if (currentTarget.classList.contains('ws-cropper_next')) {
    showCropper({ id }, index);
  } else if (currentTarget.classList.contains('ws-cropper_save')) {
    saveCrop(id);
  }
}

async function initCropper(event: Event, loaderContainer?: HTMLElement) {
  const currentTarget = event.currentTarget as HTMLElement;
  const id = currentTarget.id || currentTarget.dataset.id;
  
  const elm = document.querySelector<HTMLInputElement>(`#${id}[data-component="ws_cropper"]`);
  if (!elm) {
    return;
  }

  const modalCropper = document.querySelector<HTMLElement>(`.ws-cropper_modal[data-id="${elm.id}"]`);
  if (!modalCropper) {
    return;
  }

  let imageSrc = '';

  if (
    (event as DragEvent).dataTransfer &&
    (event as DragEvent).dataTransfer?.files &&
    (event as DragEvent).dataTransfer?.files.length
  ) {
    // if file is from drag and drop, we assign it to the elm object, to use the same logic.
    elm.files = (event as DragEvent).dataTransfer?.files || null;
  }

  if (elm.files && elm.files.length) {
    imageSrc = window.URL.createObjectURL(elm.files[0]);
  } else if (currentTarget.dataset.imageUrl) {
    imageSrc = currentTarget.dataset.imageUrl;
  }

  Loader.show(loaderContainer);

  try {
    const imgValidator = await checkImagesSizes(imageSrc, JSON.parse(elm.dataset.minimums || '{}'));

    if (!imgValidator.isValid) {
      const { error } = window.cmsTranslations.ws_cms_components.cropper;
      if (error) {
        const errorMsg = error
          .replace('%width%', imgValidator.minWidth?.toString(10) || '0')
          .replace('%height%', imgValidator.minHeight?.toString(10) || '0');

        showMessage(`${messageCropperPrefix}-${id}`, errorMsg, 'warning');
      }

      Loader.hide();
    } else {
      setPreview(elm.id, imageSrc);
      if (currentTarget.dataset.imageUrl) {
        const dataElem = document.querySelector<HTMLInputElement>(`#${id}_data`);
        if (dataElem) {
          dataElem.value = currentTarget.dataset.imageId || '';
        }
      }

      initACropper(elm);

      const cropperDetails = modalCropper.querySelector<HTMLElement>('.ws-cropper_details_obs');
      if (cropperDetails) {
        cropperDetails.innerText = '';
      }

      modalCropper.dataset.croppIndex = '1';

      document.querySelectorAll(`.ws-cropper_confirm[data-id="${elm.id}"]`).forEach(
        (input) => input.classList.remove('u-hidden'),
      );

      showCropper(elm, 0);

      if (modal) {
        if (elm.dataset.displayMode === 'list') {
          modal.refresh(`.ws-cropper_modal[data-id="${elm.id}"]`);
        } else {
          modal.open(`.ws-cropper_modal[data-id="${elm.id}"]`);
        }
      }
    }
  } catch (err) {
    Loader.hide();
    showErrorNotification(err.message);
  }
}

function init(assetElement: HTMLElement, modalElement: AModal, closeEvent: (() => void) | null = null) {
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
