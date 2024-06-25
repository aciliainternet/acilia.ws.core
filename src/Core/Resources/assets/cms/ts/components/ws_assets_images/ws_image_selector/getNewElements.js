function getNewElements(url) {
  const promiseObj = new Promise((resolve, reject) => {
    const httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', url);
    httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    httpRequest.send();
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

  return promiseObj;
}

export default getNewElements;
