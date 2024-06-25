import { initCropper as showCropper } from '../ui_cropper';

function preventDefaults(event) {
  event.preventDefault();
  event.stopPropagation();
}

function highlight(event) {
  event.currentTarget.classList.add('is-active');
}

function unhighlight(event) {
  event.currentTarget.classList.remove('is-active');
}

function handleDrop(event) {
  showCropper(event);
}

function init() {
  document.querySelectorAll('.js-img-selector-new').forEach((fileDrop) => {
    if (fileDrop) {
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
        fileDrop.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
      });

      ['dragenter', 'dragover'].forEach((eventName) => {
        fileDrop.addEventListener(eventName, highlight, false);
      });

      ['dragleave', 'drop'].forEach((eventName) => {
        fileDrop.addEventListener(eventName, unhighlight, false);
      });

      fileDrop.addEventListener('drop', handleDrop, false);
    }
  });
}

// eslint-disable-next-line no-undef
module.exports = init();
