function updateCharacterCount(textarea) {
  const maxCount = textarea.getAttribute('maxlength') || 60;
  const count = textarea.value.length;
  const helpText = textarea.nextElementSibling;

  if (helpText && helpText.tagName.toLowerCase() === 'small') {
      const countSpan = helpText.querySelector('.js-count');
      const maxCountSpan = helpText.querySelector('.js-maxCount');

      if (countSpan) {
          countSpan.textContent = count;
      }

      if (maxCountSpan) {
          maxCountSpan.textContent = maxCount;
      }
  }
}

function init() {
  const textareas = document.querySelectorAll('.js-char-count');
  textareas.forEach((textarea) => {
      updateCharacterCount(textarea);
      textarea.addEventListener('input', () => {
          updateCharacterCount(textarea);
      });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  init();
});

export default init;
