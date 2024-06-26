import Choices from 'choices.js';

let config = {};

function handlePlaceholder(choicesInput, inputElement, placeholder = '') {
  const element = inputElement;

  if (choicesInput.getValue().length === 0) {
    element.placeholder = placeholder;
    element.style.width = '100%';
  } else {
    element.placeholder = '';
  }
}

function getAddItemText(value) {
  return (`${config.addItemMessage} <b>${value}</b>`);
}

function addItemFilter(value) {
  if (!value) {
    return false;
  }

  const expression = new RegExp(config.filter, 'i');

  return expression.test(value.toLowerCase());
}

function initInputMultiple(inputMultiple, widgetConfig) {
  const choicesInput = new Choices(inputMultiple, widgetConfig);
  const inputElement = choicesInput.input.element;

  choicesInput.passedElement.element.addEventListener('addItem', () => handlePlaceholder(choicesInput, inputElement), false);
  choicesInput.passedElement.element.addEventListener(
    'removeItem', () => handlePlaceholder(choicesInput, inputElement, widgetConfig.placeholderValue), false,
  );
}

function init() {
  const { cmsSettings, cmsTranslations } = window;
  if (cmsSettings === undefined || cmsSettings === null) {
    throw Error('No CMS Settings defined.');
  }

  if (cmsTranslations === undefined || cmsTranslations === null) {
    throw Error('No CMS Translations defined.');
  }

  const inputMultiples = document.querySelectorAll('[data-component="ws_input-multiple"]');
  const inputMultipleConfig = cmsSettings.ws_cms_components.input_multiple;
  const inputMultipleTranslations = cmsTranslations.ws_cms_components.input_multiple;

  inputMultiples.forEach((inputMultiple) => {
    const { placeholder, dataset } = inputMultiple;
    const { filter } = dataset;

    config = {
      ...(filter && { addItemFilter }),
      addItemMessage: inputMultipleTranslations.add_item,
      addItemText: getAddItemText,
      classNames: {
        inputCloned: 'choices__input--cloned u-inline-block',
      },
      customAddItemText: inputMultipleTranslations.invalid_item,
      duplicateItemsAllowed: inputMultipleConfig.duplicate_items_allowed,
      editItems: inputMultipleConfig.edit_items,
      filter,
      placeholder: inputMultipleConfig.placeholder,
      placeholderValue: placeholder,
      uniqueItemText: inputMultipleTranslations.unique_item,
      removeItemButton: inputMultipleConfig.remove_item_button,
    };

    initInputMultiple(inputMultiple, config);
  });
}

export default init;
