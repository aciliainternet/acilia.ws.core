import { Controller } from '@hotwired/stimulus';
import aTooltip from '../modules/a_tooltip';

const basicConfig = {
  arrow: true,
  animation: 'fade',
};

export default class extends Controller {
  connect() {
    aTooltip([this.element], basicConfig);
  }
}
