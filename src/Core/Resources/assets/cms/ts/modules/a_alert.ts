type AlertButton = {
  text: string;
  value?: string;
};

type AlertButtons = {
  confirm?: AlertButton;
  cancel?: string;
};

type AlertOptions = {
  icon?: 'error' | 'warning' | 'info' | 'success';
  title?: string;
  text: string;
  buttons?: AlertButtons;
  dangerMode?: boolean;
}

function showSngAlert(
  options: AlertOptions | null = null,
  callback: (() => void) | null = null
): void {
  if (options && callback) {
    setAlert(options).then(callback);
  } else if (options) {
    setAlert(options);
  }
}

function setAlert(options: AlertOptions): Promise<string> {
  return new Promise((resolve) => {
    const template = getTemplate(options);
    const wrapperTemplate = document.createElement('div');
    wrapperTemplate.innerHTML = template;
    wrapperTemplate.setAttribute('data-sngularalert', 'true');
    wrapperTemplate.setAttribute('class', 'c-alert-popup');
    document.body.appendChild(wrapperTemplate);

    // User confirm action
    const confirmAction = () => {
      const alertElement = document.querySelector('[data-sngularalert]');
      if (alertElement) {
        alertElement.parentNode?.removeChild(alertElement);
      }
      if (options.buttons?.confirm?.value) {
        resolve(options.buttons.confirm.value);
      } else {
        resolve('ok');
      }
    };

    // User reject action
    const rejectAction = () => {
      const alertElement = document.querySelector('[data-sngularalert]');
      if (alertElement) {
        alertElement.parentNode?.removeChild(alertElement);
      }
    };

    const confirmButton = document.querySelector('[data-alert="confirm"]');
    if (confirmButton) {
      confirmButton.addEventListener('click', confirmAction);
    }

    const alertWrapper = document.querySelector('[data-alert="reject"]');
    if (alertWrapper) {
      alertWrapper.addEventListener('click', rejectAction);
    }
  });
}

function getTemplate(options: AlertOptions): string {
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
      icon = '<i class="fa-sharp fa-light fa-circle-info"></i>';
      break;
  }

  const acceptText =
    options.buttons?.confirm?.text ?? 'Aceptar';
  const rejectText =
    options.buttons?.cancel ?? 'Cancelar';

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
