const componentSelector = '[data-component="ws_dropdown"]';

function hideDropdown(event) {
  const elem = event.target;
  if (elem.closest('[data-component="ws_dropdown"]') === null) {
    document.querySelectorAll('[data-component="ws_dropdown"]').forEach((component) => {
      component.classList.remove('show');
      if (document.querySelector(`.${component.dataset.toggle}`)) {
        document.querySelector(`.${component.dataset.toggle}`).classList.remove('show');
      }
    });
  }
}

function toggleDropdown(event) {
  const elm = event.currentTarget;
  const isOpen = !elm.classList.contains('show');

  document.querySelectorAll('[data-component="ws_dropdown"]').forEach((component) => {
    component.classList.remove('show');
    if (document.querySelector(`.${component.dataset.toggle}`)) {
      document.querySelector(`.${component.dataset.toggle}`).classList.remove('show');
    }
  });

  if (isOpen) {
    const panel = document.querySelector(`.${elm.dataset.toggle}`);
    if (elm && panel) {
      elm.classList.toggle('show');
      panel.classList.toggle('show');
    }
  }
}

function init() {
  document.body.addEventListener('click', hideDropdown);
  document.querySelectorAll(componentSelector).forEach((elm) => {
    elm.addEventListener('click', toggleDropdown);
  });
}

export default init;
