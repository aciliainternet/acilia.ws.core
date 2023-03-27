import { Controller } from '@hotwired/stimulus';
import aRangeSlider from '../modules/a_rangeSlider';

export default class extends Controller<HTMLInputElement> {
  static values = {
    min: Number,
    max: Number,
    step: Number,
  };

  declare minValue: number;

  declare maxValue: number;

  declare stepValue: number;

  connect() {
    const options = {
      min: this.minValue,
      max: this.maxValue,
      step: this.stepValue,
    };

    aRangeSlider(this.element, options);
  }
}
