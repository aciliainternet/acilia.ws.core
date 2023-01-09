import { Controller } from '@hotwired/stimulus';
import aRangeSlider from '../modules/a_rangeSlider';

export default class extends Controller<HTMLElement> {
  static values = {
    min: Number,
    max: Number,
    step: Number,
  };

  declare minValue: Number;
  declare maxValue: Number;
  declare stepValue: Number;

  connect() {
    const options = {
      min: this.minValue,
      max: this.maxValue,
      step: this.stepValue,
    };

    aRangeSlider(this.element, options);
  }
}
