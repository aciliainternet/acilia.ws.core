function updateCharacterCount(textarea) {
  const maxCount = textarea.getAttribute('maxlength');
  const count = textarea.value.length;
  const helpText = textarea.nextElementSibling;

  if (helpText && helpText.tagName.toLowerCase() === 'small') {
    helpText.textContent = `${count} out of ${maxCount} characters recommended`;
  }
}

function init() {
  const textareas = document.querySelectorAll('.js-char-count');
  textareas.forEach((textarea) => {
      textarea.addEventListener('input', () => {
          updateCharacterCount(textarea);
      });
  });
}

export default init;
