import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = [ 'removeField', 'linkField', 'formUpload']
  
  declare removeFieldTarget: HTMLInputElement;
  declare hasLinkFieldTarget: boolean;
  declare linkFieldTarget: HTMLElement;
  declare formUploadTarget: HTMLElement;

  deleteAsset() {
    this.removeFieldTarget.value = 'remove';

    if (this.hasLinkFieldTarget) {
      this.linkFieldTarget.classList.add('u-hidden');
    }
    
    this.formUploadTarget.classList.remove('u-hidden');
  }
}
