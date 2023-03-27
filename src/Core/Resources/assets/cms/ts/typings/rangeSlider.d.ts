interface RangeSliderOptions {
  min: number;
  max: number;
  step: number;
  polyfill: boolean;
  root: Document | HTMLElement;
}

interface HTMLRangeSliderElement extends HTMLInputElement {
  rangeSlider: HTMLRangeSliderElement;
  handle: HTMLInputElement;
}

declare module 'rangeslider-pure' {
  declare function create(
    element: HTMLInputElement,
    options: RangeSliderOptions
  ): void;
}
