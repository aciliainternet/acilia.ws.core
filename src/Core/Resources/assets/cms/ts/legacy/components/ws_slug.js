function validateInput(event) {
  const regex = new RegExp('^[A-Za-z0-9 _-]*$');

  if (!regex.test(event.key)) {
    event.preventDefault();
  }
}

function sanitizeInput(event) {
  const current = event.currentTarget;
  current.value = current.value.toLowerCase();
  current.value = current.value.replace(/\s+/g, '-');
  current.value = current.value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

function init() {
  document.querySelectorAll('[data-component="ws_slug"]').forEach((elm) => {
    elm.addEventListener('keyup', sanitizeInput);
    elm.addEventListener('keydown', validateInput);
  });
}

export default init;
