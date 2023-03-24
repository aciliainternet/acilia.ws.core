import { initCropper as showCropper } from '../ui_cropper';

function preventDefaults(event: Event) {
  event.preventDefault();
  event.stopPropagation();
}

function highlight(event: Event) {
  (event.currentTarget as HTMLElement).classList.add('is-active');
}

function unhighlight(event: Event) {
  (event.currentTarget as HTMLElement).classList.remove('is-active');
}

function handleDrop(event: Event) {
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

module.exports = init();
