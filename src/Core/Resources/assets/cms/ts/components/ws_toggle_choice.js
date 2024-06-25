function handleToggle(event) {
  const { target, currentTarget } = event;
  const element = !target.classList.contains('js-toggle-btn') ? target.closest('.js-toggle-btn') : target;

  if (element.classList.contains('js-toggle-btn')) {
    const sibling = element.nextElementSibling || element.previousElementSibling;

    if (currentTarget.querySelector('.js-toggle-input:checked')) {
      currentTarget.querySelector('.js-toggle-input:checked').checked = false;
    }

    sibling.classList.remove('is-active');
    element.classList.add('is-active');
    element.querySelector('.js-toggle-input').checked = true;
  }
}

function init() {
  document.querySelectorAll('[data-component="ws_toggle_choice"]').forEach((elm) => {
    elm.addEventListener('click', handleToggle);
  });
}

export default init;
