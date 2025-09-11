import Cropper from 'cropperjs';

const cropperInstances = [];

function crop(image, config) {
  return new Cropper(image, config);
}

function getCropperInstance(id) {
  return cropperInstances[id];
}

function createCropperConfig(element) {
  const croppersConfig = [];
  const ratios = JSON.parse(element.dataset.ratios);
  const minimums = JSON.parse(element.dataset.minimums);
  const cropperArea = parseFloat(element.dataset.cropperArea ?? .8);

  Object.keys(ratios).forEach((ratioKey) => {
    croppersConfig.push({
      ratio: ratios[ratioKey].label,
      ratioValue: ratios[ratioKey].fraction,
      minimums: minimums[ratioKey],
      autoCropArea: cropperArea
    });
  });

  cropperInstances[element.id] = { config: croppersConfig, cropper: null };
}

function init(element) {
  if (cropperInstances[element.id] === undefined) {
    createCropperConfig(element);
  }
}

export {
  init,
  crop,
  getCropperInstance,
};
