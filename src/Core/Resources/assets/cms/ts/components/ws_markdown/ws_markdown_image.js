let newImage = null;

function getImage(file) {
  return new Promise((resolve, reject) => {
    const httpRequest = new XMLHttpRequest();
    const formData = new FormData();
    formData.append('asset', file);
    httpRequest.open('POST', window.cmsSettings.ws_cms_components.markdown_asset_image.endpoint);
    httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    httpRequest.send(formData);
    httpRequest.onreadystatechange = () => {
      if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
          resolve(JSON.parse(httpRequest.response));
        } else if (httpRequest.status === 500
          || httpRequest.status === 400
          || httpRequest.status === 403
          || httpRequest.status === 404) {
          reject(httpRequest.status);
        }
      }
    };
  });
}

function handleImage() {
  return new Promise(((resolve) => {
    const interval = setInterval(() => {
      const tmpImage = newImage;
      if (tmpImage != null) {
        newImage = null;
        clearInterval(interval);
        resolve(tmpImage);
      }
    }, 10);
  }));
}

function init() {
  if (document.querySelector('[data-component="ws_markdown_image"]')) {
    document.querySelector('[data-component="ws_markdown_image"]').addEventListener('change', (event) => {
      getImage(event.currentTarget.files[0]).then((image) => { newImage = image; });
    });
  }
}

export {
  init,
  handleImage,
};
