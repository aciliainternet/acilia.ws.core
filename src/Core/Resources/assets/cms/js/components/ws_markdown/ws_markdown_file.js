let newFile = null;

function getFile(file) {
  return new Promise((resolve, reject) => {
    const httpRequest = new XMLHttpRequest();
    const formData = new FormData();
    formData.append('asset', file);
    httpRequest.open('POST', window.cmsSettings.ws_cms_components.markdown_asset_file.endpoint);
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

function handleFile() {
  return new Promise(((resolve) => {
    const interval = setInterval(() => {
      const tmpFile = newFile;
      if (tmpFile != null) {
        newFile = null;
        clearInterval(interval);
        resolve(tmpFile);
      }
    }, 10);
  }));
}

function init() {
  const fileElement = document.querySelector('[data-component="ws_markdown_file"]');
  if (fileElement === null) {
    return;
  }

  fileElement.addEventListener('change', (event) => {
    getFile(event.currentTarget.files[0]).then((file) => { newFile = file; });
  });
}

export {
  init,
  handleFile,
};
