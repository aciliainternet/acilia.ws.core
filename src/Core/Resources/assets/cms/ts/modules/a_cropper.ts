import Cropper from 'cropperjs';

interface Ratios {
  [label: string]: { label: string; fraction: string };
}

interface Minimums {
  [label: string]: number;
}

interface Config {
  ratio: string;
  ratioValue: string;
  minimums: Minimums;
  data?: Cropper.Data;
}

export interface CropperInstance {
  config: Config[];
  cropper: Cropper | null;
}

const cropperInstances: Record<string, CropperInstance> = {};

function crop(
  image: HTMLImageElement | HTMLCanvasElement,
  config?: Cropper.Options
) {
  return new Cropper(image, config);
}

function getCropperInstance(id: string): CropperInstance {
  return cropperInstances[id];
}

function createCropperConfig(element: HTMLElement) {
  const croppersConfig: Config[] = [];
  const ratios: Ratios = JSON.parse(element.dataset.ratios || '{}');
  const minimums: Record<string, Minimums> = JSON.parse(
    element.dataset.minimums || '{}'
  );

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

export { init, crop, getCropperInstance };
