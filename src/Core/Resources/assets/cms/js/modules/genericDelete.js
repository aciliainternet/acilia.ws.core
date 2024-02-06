import showSngAlert from './a_alert-v2';

function onRemoveDone(event) {
  const request = event.currentTarget;
  const response = JSON.parse(request.response);

  switch (request.status) {
    case 500:
    case 403:
    case 404:
      showSngAlert({
        title: response.title ?? 'Error',
        text: response.msg,
        icon: 'error',
      });
      break;
    case 200:
      if (response.id) {
        const selector = `.js-genericDelete_wrapper[data-id="${response.id}"]`;
        document.querySelector(selector).classList.add('js-genericDelete_remove');
      }
      showSngAlert({
        title: response.title,
        text: response.msg,
        icon: 'success',
      }, () => {
        const elm = document.querySelector('.js-genericDelete_remove');
        if (elm) {
          elm.classList.add('animated', 'fadeOutRight');
          setTimeout(() => {
            document.querySelector('.js-genericDelete_remove').remove();
          }, 800);

          const pagSubtotalValue = document.getElementById('js-pagination_subtotal');
          if (pagSubtotalValue) {
            pagSubtotalValue.textContent = parseInt(pagSubtotalValue.textContent, 10) - 1;
          }

          const pagTotalValue = document.getElementById('js-pagination_total');
          if (pagTotalValue) {
            pagTotalValue.textContent = parseInt(pagTotalValue.textContent, 10) - 1;
          }
        }
      });
      break;
    case 302:
      showSngAlert({
        title: response.title,
        text: response.msg,
        icon: 'success',
      });
      if (response.href) {
        setTimeout(() => {
          window.location = response.href;
        }, 2000);
      }
      break;
    default:
      break;
  }
}

function sendDeletePost(value) {
  const elm = document.querySelector(`.js-genericDelete[data-id="${value}"]`);
  if (elm) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', elm.dataset.url);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest', 'Content-Type', 'application/json');
    xhr.onload = onRemoveDone;
    xhr.onerror = onRemoveDone;
    xhr.send(JSON.stringify({ id: elm.dataset.id }));
  }
}

export function remove(event) {
  const { title, message, id } = event.currentTarget.dataset;
  showSngAlert({
    icon: 'warning',
    dangerMode: true,
    title,
    text: message,
    buttons: {
      cancel: window.cmsTranslations.cancel,
      confirm: {
        text: window.cmsTranslations.delete.confirm,
        value: id,
        closeModal: false,
      },
    },
  }, (value) => {
    sendDeletePost(value);
  });
}

function init() {
  document.querySelectorAll('.js-genericDelete').forEach((input) => input.addEventListener('click', remove));
}

export default init();
