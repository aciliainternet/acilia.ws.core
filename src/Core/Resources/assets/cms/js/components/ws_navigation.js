function init() {
  const newItemBtn = document.querySelector('.js-navigation-new-item-btn');

  if (newItemBtn) {
    newItemBtn.addEventListener('click', () => {
      const newItemForm = document.querySelector('.js-navigation-new-item-form');

      if (newItemForm) {
        newItemForm.classList.toggle('u-hidden');
      }
    });
  }
}

export default init;
