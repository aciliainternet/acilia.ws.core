const componentSelector = '[data-component="ws_table_collapse"]';

function toggleRow(event) {
  const elm = event.currentTarget;

  const panel = document.querySelector(`${componentSelector} tr#${elm.dataset.toggle}`);
  if (elm && panel) {
    elm.querySelector('.c-table__toggle').classList.toggle('is-open');
    panel.classList.toggle('is-active');
  }
}

function init() {
  document.querySelectorAll(`${componentSelector} .ws-table-collapse`).forEach((elm) => {
    elm.addEventListener('click', toggleRow);
  });
}

export default init;
