function loadImage(imageSrc) {
  return new Promise((resolve, reject) => {
    const img = document.createElement('img');
    img.src = imageSrc;
    img.onload = () => resolve(img);
    img.onerror = (e) => reject(e);
  });
}

function sortMethod(a, b) {
  return a < b ? 1 : -1;
}

export default async function checkImagesSizes(imageSrc, minimums) {
  try {
    const imageTag = await loadImage(imageSrc);
    const validatorData = { isValid: true };
    const minWidth = Object
      .values(minimums)
      .map((m) => m.width)
      .sort(sortMethod)
      .shift();
    const minHeight = Object
      .values(minimums)
      .map((m) => m.height)
      .sort(sortMethod)
      .shift();

    if (minHeight !== undefined && minWidth !== undefined) {
      if (imageTag.naturalHeight < minHeight || imageTag.naturalWidth < minWidth) {
        validatorData.isValid = false;
        validatorData.minHeight = minHeight;
        validatorData.minWidth = minWidth;
      }
    }

    return validatorData;
  } catch (err) {
    const { currentTarget } = err;
    throw new Error(`Failed to load image for src: ${currentTarget.src}`);
  }
}
