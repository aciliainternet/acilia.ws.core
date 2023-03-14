import Cropper from 'cropperjs';

const cropperInstances = [];

function crop(image: HTMLImageElement | HTMLCanvasElement, config?: Cropper.Options) {
  return new Cropper(image, config);
}

function getCropperInstance(id: string) {
  return cropperInstances[id];
}

interface Ratios {
  [label: string]: { label: string, fraction: string };
}

interface Minimums {
  [label: string]: any;
}

interface Config {
  ratio: string;
  ratioValue: string;
  minimums: Minimums;
}

function createCropperConfig(element: HTMLElement) {
  const croppersConfig: Config[] = [];
  const ratios: Ratios = JSON.parse(element.dataset.ratios || "{}");
  const minimums: Minimums = JSON.parse(element.dataset.minimums || "{}");

  Object.keys(ratios).forEach((ratioKey) => {
    croppersConfig.push({
      ratio: ratios[ratioKey].label,
      ratioValue: ratios[ratioKey].fraction,
      minimums: minimums[ratioKey],
    });
  });

  cropperInstances[element.id] = { config: croppersConfig, cropper: null };
}

function init(element: HTMLElement) {
  if (cropperInstances[element.id] === undefined) {
    createCropperConfig(element);
  }
}

export {
  init,
  crop,
  getCropperInstance,
};
