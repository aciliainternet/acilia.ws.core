import getNewElements from './getNewElements';
import { init as lazyLoadInit, update as lazyLoadUpdate } from '../../../modules/a_lazyload';
import { initCropper as showCropper } from '../ui_cropper';
import { show as showLoader, hide as hideLoader } from '../../ws_loader';
import { show as showMessage } from '../ui_messages';
import { showError as showErrorNotification } from '../../../modules/a_notifications';
import checkImagesSizes from '../imageSizeValidator';

let imageListContainer = null;
let nextPage = 0;
let stillData = true;
let working = false;
let dataId = '';
let endpointUrl = '';

function removeListElements(list) {
  list.querySelectorAll('.js-image-item').forEach((element) => {
    list.removeChild(element);
  });
}

function setNextPage(page) {
  nextPage = page;
}

function openCropper(event) {
  showCropper(event, imageListContainer.closest('.js-image-selector-modal'));
}

async function useImage(event) {
  const { id } = event.currentTarget.closest('.js-img-selector-images-list').dataset;
  const { imageId, imageOriginal, imageUrl } = event.currentTarget.dataset;
  const cropper = document.querySelector(`#${id}[data-component="ws_cropper"]`);

  showLoader(imageListContainer.closest('.js-image-selector-modal'));

  try {
    const imgValidator = await checkImagesSizes(imageOriginal, JSON.parse(cropper.dataset.minimums));

    if (!imgValidator.isValid) {
      const { error } = window.cmsTranslations.ws_cms_components.cropper;
      if (error) {
        const errorMsg = error.replace('%width%', imgValidator.minWidth).replace('%height%', imgValidator.minHeight);
        showMessage(`.js-cropper-msg-${id}`, errorMsg, 'warning');
      }
      hideLoader();
    } else {
      const element = document.querySelector(`[data-id="${id.replace('_asset', '')}"]`);
      if (element.querySelector('img')) {
        element.querySelector('img').src = imageUrl;
      } else if (element.querySelector('.c-img-upload__wrapper-img')) {
        element.querySelector('.c-img-upload__wrapper-img').insertAdjacentHTML(
          'afterbegin',
          `<img class="c-img-upload__img" src="${imageUrl}">`,
        );
        element.classList.remove('u-hidden');
      }

      if (document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${id}"] i`)) {
        document.querySelector(`.js-open-modal.js-not-on-preview[data-id-asset-component="${id}"]`)
          .classList.add('u-hidden');
      }

      hideLoader();
      document.getElementById(`${id}_data`).value = imageId;
      document.querySelector('[data-id="image-selector"]').querySelector('#a-close').click();
    }
  } catch (err) {
    hideLoader();
    showErrorNotification(err.message);
  }
}

function deleteImage(event) {
  const { dataset } = event.currentTarget;
  const httpRequest = new XMLHttpRequest();

  if (dataset.path) {
    httpRequest.open('POST', dataset.path);
    httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    showLoader(imageListContainer);
    httpRequest.onreadystatechange = () => {
      const imgToRemove = imageListContainer.querySelector(`img[id="${dataset.imageId}"]`);

      if (imgToRemove && imgToRemove.parentElement) {
        imageListContainer.removeChild(imgToRemove.parentElement);
      }
      hideLoader();
    };
    httpRequest.send(JSON.stringify({ assetId: dataset.imageId }));
  }
}

function showElements(imageList) {
  const { id } = document.querySelector(`.js-image-selector-modal[data-id="${dataId}"]`).dataset;
  const imgTemplate = document.querySelector(`[data-id="${dataId}"].js-image-item`).outerHTML;

  if (imageList.length > 0) {
    imageList.forEach((element) => {
      imageListContainer.insertAdjacentHTML(
        'beforeend',
        imgTemplate
          .replace(/#image-alt/g, element.alt)
          .replace(/#image-thumb/g, element.thumb)
          .replace(/#id/g, id)
          .replace(/#image-id/g, element.id)
          .replace(/#image-original/g, element.image)
          .replace(/#extra-class/g, 'is-visible'),
      );

      imageListContainer.lastChild.querySelector('.js-list-image-crop').addEventListener('click', openCropper);
      imageListContainer.lastChild.querySelector('.js-list-image-use').addEventListener('click', useImage);
      imageListContainer.lastChild.querySelector('.js-list-image-delete').addEventListener('click', deleteImage);
    });

    lazyLoadUpdate();
  } else {
    stillData = false;
    imageListContainer.insertAdjacentHTML(
      'beforeend',
      `<figure class="c-img-modal__figure c-img-modal__figure--text js-no-more-images">
          <i class="c-img-modal__figure-icon fa fa-picture-o" aria-hidden="true"></i>
          <p class="c-img-modal__figure-text">
            ${window.cmsTranslations.ws_cms_components.assets_images.no_results}
          </p>
      </figure>`
      ,
    );
  }

  hideLoader();
  working = false;
}

function getElementsOnScroll() {
  const {
    y: yLastElement,
    height: heightLastElement,
  } = imageListContainer.lastElementChild.getBoundingClientRect();

  const {
    y: yContainer,
    height: heightContainer,
  } = imageListContainer.getBoundingClientRect();

  // On the container there is a diffenece of height between firefox and chrome
  const gap = 10;

  if (
    stillData
    && !working
    && !imageListContainer.lastElementChild.classList.contains('js-loader')
    && yLastElement + heightLastElement <= (yContainer + heightContainer) + gap
  ) {
    working = true;
    showLoader(imageListContainer);

    if (document.querySelector('.js-search-form').dataset.queryString) {
      endpointUrl = `${endpointUrl}&${document.querySelector('.js-search-form').dataset.queryString}`;
    }

    getNewElements(endpointUrl.replace(/\?page=[^/]*$/, `?page=${nextPage}`)).then(showElements);
    setNextPage(nextPage += 1);
  }
}

function init(containerId = null) {
  if (containerId) {
    dataId = containerId;
    endpointUrl = window.cmsSettings.ws_cms_components.assets_images.endpoint;
    imageListContainer = document.querySelector(`.js-img-selector-images-list[data-id="${containerId}"]`);

    if (imageListContainer.querySelector('.js-no-more-images')) {
      imageListContainer.removeChild(imageListContainer.querySelector('.js-no-more-images'));
    }

    if (!stillData) {
      stillData = true;
    }

    nextPage = parseInt(imageListContainer.dataset.nextPage, 10);
    endpointUrl = `${endpointUrl}?page=1`;
    imageListContainer.addEventListener('scroll', getElementsOnScroll);
    removeListElements(imageListContainer);
    showLoader(imageListContainer);
    getNewElements(endpointUrl).then(showElements);
    lazyLoadInit();
  }
}

export {
  init,
  setNextPage,
  showElements,
  removeListElements,
};
