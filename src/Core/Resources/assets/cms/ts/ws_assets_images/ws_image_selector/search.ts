import getNewElements from './getNewElements';
import { showElements, setNextPage, removeListElements } from './imageList';
import { Loader } from '../../tools/ws_loader';

let endpointUrl = '';

function restartListImages() {
  const imageList = document.querySelector<HTMLElement>(
    '.js-img-selector-images-list'
  );

  if (imageList) {
    removeListElements(imageList);
  }

  getNewElements(endpointUrl.replace(/\?f=[^/]*$/, '')).then(showElements);
}

function searchAction() {
  const searchForm = document.querySelector<HTMLFormElement>('.js-search-form');
  const searchInput =
    document.querySelector<HTMLInputElement>('.js-search-input')?.value;
  const imageList = document.querySelector<HTMLElement>(
    '.js-img-selector-images-list'
  );

  if (!searchForm || !imageList) {
    return;
  }

  endpointUrl = `${endpointUrl}?f=`.replace(/\?f=[^/]*$/, `?f=${searchInput}`);
  searchForm.dataset.querySearch = `f=${searchInput}`;

  setNextPage(2);
  removeListElements(imageList);
  Loader.show(imageList);
  getNewElements(endpointUrl).then(showElements);
}

function handleKeyPressed(event: KeyboardEvent) {
  const currentTarget = event.currentTarget as HTMLInputElement;
  if (
    (event.keyCode === 8 || event.keyCode === 46) &&
    currentTarget.value === ''
  ) {
    restartListImages();
  } else if (event.keyCode === 13) {
    searchAction();
  }
}

export default function init() {
  endpointUrl = window.cmsSettings.ws_cms_components.assets_images.endpoint;
  const inputElement =
    document.querySelector<HTMLInputElement>('.js-search-input');
  const submitElement = document.querySelector('.js-search-submit');

  if (inputElement && submitElement) {
    inputElement.addEventListener('keyup', handleKeyPressed);
    submitElement.addEventListener('click', searchAction);
  }
}
