import getNewElements from './getNewElements';
import { showElements, setNextPage, removeListElements } from './imageList';
import { show as showLoader } from '../../ws_loader';

let endpointUrl = window.cmsSettings.ws_cms_components.assets_images.endpoint;

function restartListImages() {
  const imageList = document.querySelector('.js-img-selector-images-list');
  removeListElements(imageList);
  getNewElements(endpointUrl.replace(/\?f=[^/]*$/, '')).then(showElements);
}

function searchAction() {
  const searchForm = document.querySelector('.js-search-form');
  const searchInput = document.querySelector('.js-search-input').value;
  const imageList = document.querySelector('.js-img-selector-images-list');

  endpointUrl = `${endpointUrl}?f=`.replace(/\?f=[^/]*$/, `?f=${searchInput}`);
  searchForm.dataset.querySearch = `f=${searchInput}`;

  setNextPage(2);
  removeListElements(imageList);
  showLoader(imageList);
  getNewElements(endpointUrl).then(showElements);
}

function handleKeyPressed(event) {
  if ((event.keyCode === 8 || event.keyCode === 46) && event.currentTarget.value === '') {
    restartListImages();
  } else if (event.keyCode === 13) {
    searchAction();
  }
}

function init() {
  if (document.querySelector('.js-search-input') && document.querySelector('.js-search-submit')) {
    document.querySelector('.js-search-input').addEventListener('keyup', handleKeyPressed);
    document.querySelector('.js-search-submit').addEventListener('click', searchAction);
  }
}

module.exports = init();
