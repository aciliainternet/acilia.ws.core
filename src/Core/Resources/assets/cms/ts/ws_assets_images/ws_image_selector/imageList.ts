import '../../typings/global.d';
import getNewElements, { NewElementsResponse } from './getNewElements';
import {
  init as lazyLoadInit,
  update as lazyLoadUpdate,
} from '../../modules/a_lazyload';
import { initCropper as showCropper } from '../ui_cropper';
import { Loader } from '../../tools/ws_loader';
import { show as showMessage } from '../ui_messages';
import { showError as showErrorNotification } from '../../modules/a_notifications';
import checkImagesSizes from '../../tools/imageSizeValidator';

let imageListContainer: HTMLElement | null = null;
let nextPage = 0;
let stillData = true;
let working = false;
let dataId = '';
let endpointUrl = '';

function removeListElements(list: HTMLElement) {
  list.querySelectorAll('.js-image-item').forEach((element) => {
    list.removeChild(element);
  });
}

function setNextPage(page: number) {
  nextPage = page;
}

function openCropper(event: Event) {
  const modal = imageListContainer?.closest<HTMLElement>(
    '.js-image-selector-modal'
  );

  if (modal) {
    showCropper(event, modal);
  }
}

async function useImage(event: Event) {
  const currentTarget = event.currentTarget as HTMLElement;
  const id = currentTarget.closest<HTMLElement>('.js-img-selector-images-list')
    ?.dataset.id;
  const { imageId, imageOriginal, imageUrl } = currentTarget.dataset;
  const cropper = document.querySelector<HTMLElement>(
    `#${id}[data-component="ws_cropper"]`
  );

  Loader.show(imageListContainer?.closest('.js-image-selector-modal'));

  if (!imageOriginal || !cropper || !id || !imageUrl || !imageId) {
    return;
  }

  try {
    const { minimums } = cropper.dataset;
    if (!minimums) {
      return;
    }

    const imgValidator = await checkImagesSizes(
      imageOriginal,
      JSON.parse(minimums)
    );

    if (!imgValidator.isValid) {
      const { error } = window.cmsTranslations.ws_cms_components.cropper;

      if (error) {
        const errorMsg = error
          .replace('%width%', imgValidator.minWidth?.toString(10) || '')
          .replace('%height%', imgValidator.minHeight?.toString(10) || '');

        showMessage(`.js-cropper-msg-${id}`, errorMsg, 'warning');
      }

      Loader.hide();
    } else {
      const element = document.querySelector<HTMLElement>(
        `[data-id="${id.replace('_asset', '')}"]`
      );
      const img = element?.querySelector<HTMLImageElement>('img');
      const wrapperImg = element?.querySelector('.c-img-upload__wrapper-img');

      if (img) {
        img.src = imageUrl;
      } else if (wrapperImg) {
        wrapperImg.insertAdjacentHTML(
          'afterbegin',
          `<img class="c-img-upload__img" src="${imageUrl}">`
        );

        if (element) {
          element.classList.remove('u-hidden');
        }
      }

      const notOnPreview = document.querySelector<HTMLElement>(
        `.js-open-modal.js-not-on-preview[data-id-asset-component="${id}"] i`
      );

      if (notOnPreview) {
        notOnPreview.classList.add('u-hidden');
      }

      Loader.hide();

      const dataIdElem = document.querySelector<HTMLInputElement>(
        `#${id}_data`
      );
      if (dataIdElem) {
        dataIdElem.value = imageId;
      }

      const closeBtn = document
        .querySelector('[data-id="image-selector"]')
        ?.querySelector<HTMLElement>('#a-close');

      if (closeBtn) {
        closeBtn.click();
      }
    }
  } catch (err: unknown) {
    Loader.hide();
    showErrorNotification((err as Error).message);
  }
}

function deleteImage(event: Event) {
  const { dataset } = event.currentTarget as HTMLElement;
  const httpRequest = new XMLHttpRequest();

  if (dataset.path) {
    httpRequest.open('POST', dataset.path);
    httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    Loader.show(imageListContainer);
    httpRequest.onreadystatechange = () => {
      const imgToRemove = imageListContainer?.querySelector(
        `img[id="${dataset.imageId}"]`
      );

      if (imgToRemove && imgToRemove.parentElement) {
        imageListContainer?.removeChild(imgToRemove.parentElement);
      }
      Loader.hide();
    };

    httpRequest.send(JSON.stringify({ assetId: dataset.imageId }));
  }
}

function showElements(imageList: NewElementsResponse) {
  const id = document.querySelector<HTMLElement>(
    `.js-image-selector-modal[data-id="${dataId}"]`
  )?.dataset.id;
  const imgTemplate = document.querySelector(
    `[data-id="${dataId}"].js-image-item`
  )?.outerHTML;

  if (!id || !imgTemplate) {
    return;
  }

  if (imageList.length > 0) {
    imageList.forEach((element) => {
      imageListContainer?.insertAdjacentHTML(
        'beforeend',
        imgTemplate
          .replace(/#image-alt/g, element.alt)
          .replace(/#image-thumb/g, element.thumb)
          .replace(/#id/g, id)
          .replace(/#image-id/g, element.id)
          .replace(
            /#image-original/g,
            (element as unknown as { image: string }).image
          )
          .replace(/#extra-class/g, 'is-visible')
      );

      const lastChild = imageListContainer?.lastChild as HTMLElement;
      if (lastChild) {
        lastChild
          .querySelector('.js-list-image-crop')
          ?.addEventListener('click', openCropper);
        lastChild
          .querySelector('.js-list-image-use')
          ?.addEventListener('click', useImage);
        lastChild
          .querySelector('.js-list-image-delete')
          ?.addEventListener('click', deleteImage);
      }
    });

    lazyLoadUpdate();
  } else {
    stillData = false;
    imageListContainer?.insertAdjacentHTML(
      'beforeend',
      `<figure class="c-img-modal__figure c-img-modal__figure--text js-no-more-images">
          <i class="c-img-modal__figure-icon fa fa-picture-o" aria-hidden="true"></i>
          <p class="c-img-modal__figure-text">
            ${window.cmsTranslations.ws_cms_components.assets_images.no_results}
          </p>
      </figure>`
    );
  }

  Loader.hide();
  working = false;
}

function getElementsOnScroll() {
  const boundingRectLastChild =
    imageListContainer?.lastElementChild?.getBoundingClientRect();
  const boundingRect = imageListContainer?.getBoundingClientRect();

  if (!boundingRect || !boundingRectLastChild) {
    return;
  }

  const { y: yLastElement, height: heightLastElement } = boundingRectLastChild;
  const { y: yContainer, height: heightContainer } = boundingRect;

  // On the container there is a diffenece of height between firefox and chrome
  const gap = 10;

  if (
    stillData &&
    !working &&
    !imageListContainer?.lastElementChild?.classList.contains('js-loader') &&
    yLastElement + heightLastElement <= yContainer + heightContainer + gap
  ) {
    working = true;
    Loader.hide();

    const queryString =
      document.querySelector<HTMLElement>('.js-search-form')?.dataset
        .queryString;

    if (queryString) {
      endpointUrl = `${endpointUrl}&${queryString}`;
    }

    getNewElements(
      endpointUrl.replace(/\?page=[^/]*$/, `?page=${nextPage}`)
    ).then(showElements);

    setNextPage((nextPage += 1));
  }
}

function init(containerId: string | null = null) {
  if (containerId) {
    dataId = containerId;
    endpointUrl = window.cmsSettings.ws_cms_components.assets_images.endpoint;
    imageListContainer = document.querySelector(
      `.js-img-selector-images-list[data-id="${containerId}"]`
    );

    const noMoreImages =
      imageListContainer?.querySelector('.js-no-more-images');
    if (noMoreImages) {
      imageListContainer?.removeChild(noMoreImages);
    }

    if (!stillData) {
      stillData = true;
    }

    nextPage = parseInt(imageListContainer?.dataset.nextPage || '0', 10);
    endpointUrl = `${endpointUrl}?page=1`;

    if (imageListContainer) {
      imageListContainer.addEventListener('scroll', getElementsOnScroll);
      removeListElements(imageListContainer);
    }

    Loader.hide();
    getNewElements(endpointUrl).then(showElements);
    lazyLoadInit();
  }
}

export { init, setNextPage, showElements, removeListElements };
