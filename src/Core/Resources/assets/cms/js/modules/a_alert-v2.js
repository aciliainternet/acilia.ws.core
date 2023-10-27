function showSngAlert(options = null, callback = null) {
  if (options && callback) {
    setAlert(options).then(callback);
  } else if (options) {
    setAlert(options);
  }
}

function setAlert(options) {
  return new Promise((resolve, reject) => {
    const template =  getTemplate(options);
    const wrapperTemplate = document.createElement('div');
    wrapperTemplate.innerHTML = template;
    wrapperTemplate.setAttribute('data-sngularalert', 'true');
    wrapperTemplate.setAttribute('class', 'c-alert-popup');
    document.body.appendChild(wrapperTemplate);

    // user confirm action
    const confirmAction = () => {
      const alertElement = document.querySelector('[data-sngularalert]');
      if (alertElement) {
        alertElement.parentNode.removeChild(alertElement);
      }
      if (options.buttons?.confirm?.value) {
        resolve(options.buttons.confirm.value);
      } else {
        resolve("ok");
      }
    };

    // user reject action
    const rejectAction = () => {
      const alertElement = document.querySelector('[data-sngularalert]');
      if (alertElement) {
        alertElement.parentNode.removeChild(alertElement);
      }
      // reject("ko");
    };
    if (document.querySelector('[data-alert="confirm"]')) {
      document.querySelector('[data-alert="confirm"]').addEventListener("click", confirmAction);
    }
    if (document.querySelector('[data-alert="confirm"]')) {
      document.querySelector('[data-alert="confirm"]').addEventListener("click", confirmAction);
    }
    document.querySelector('[data-sngularalert]').addEventListener("click", rejectAction);
  });
}

function getTemplate(options) {
  let icon = '<i class="fa-light fa-circle-check"></i>';
  switch (options.icon) {
    case 'error':
      icon = '<i class="fa-sharp fa-regular fa-xmark"></i>';
      break;
    case 'warning':
      icon = '<i class="fa-light fa-circle-exclamation"></i>';
      break;
    case 'info':
      icon = '<i class="fa-sharp fa-light fa-circle-info"></i>';
      break;
    case 'success':
      icon = '<i class="fa-light fa-circle-check"></i>';
      break;
    default:
      icon = success;
  }
  const acceptText = options.buttons && options.buttons.confirm ? options.buttons.confirm.text : 'Aceptar';
  const rejectText = options.buttons && options.buttons.cancel ? options.buttons.cancel : 'Cancelar';
  let dangerMode = '';
  if (options.dangerMode) {
    dangerMode = 'c-btn--danger';
  }
  let buttons = `<button class="c-btn c-btn--solid c-alert-popup__btn c-alert-popup__btn--confirm ${dangerMode}" data-alert="confirm">${acceptText}</button>`;
  if (options.buttons?.cancel) {
    buttons = `<button class="c-btn c-alert-popup__btn" data-alert="reject">${rejectText}</button>${buttons}`;
  }

  return `
    <div class="c-alert-popup__wrapper">
      <span class="c-alert-popup__icon">${icon}</span>
      <h3 class="c-alert-popup__heading">${options.title}</h3>
      <p class="c-alert-popup__text">${options.text}</p>
      <div class="c-alert-popup__btn-wrapper">${buttons}</div>
    </div>
  `;
}

export default showSngAlert;
