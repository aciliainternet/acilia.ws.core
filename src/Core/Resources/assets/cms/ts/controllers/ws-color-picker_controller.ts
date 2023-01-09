import { Controller } from '@hotwired/stimulus';
import aColorPicker from '../modules/a_colorPicker';

export default class extends Controller<HTMLElement> {
  connect(): void {
      aColorPicker(this.element);
  }
}
