import { Controller } from '@hotwired/stimulus';
import Choices, { Choices as ChoicesNamespace } from 'choices.js';

export default class extends Controller<HTMLInputElement | HTMLSelectElement> {
  config: Partial<ChoicesNamespace.Options> & { addItemMessage: string, filter: string };

  connect() {
    const { cmsSettings, cmsTranslations } = window;
    if (cmsSettings === undefined || cmsSettings === null) {
      throw Error('No CMS Settings defined.');
    }

    if (cmsTranslations === undefined || cmsTranslations === null) {
      throw Error('No CMS Translations defined.');
    }

    const inputMultipleConfig = cmsSettings.ws_cms_components.input_multiple;
    const inputMultipleTranslations = cmsTranslations.ws_cms_components.input_multiple;

    this.addItemFilter = this.addItemFilter.bind(this);
    this.getAddItemText = this.getAddItemText.bind(this);

    const placeholder = this.element.getAttribute('placeholder');
    const filter = this.element.dataset.filter || '';

    this.config = {
      ...(filter && { addItemFilter: this.addItemFilter }),
      addItemMessage: inputMultipleTranslations.add_item,
      addItemText: this.getAddItemText,
      classNames: {
        inputCloned: 'choices__input--cloned u-inline-block',
      } as ChoicesNamespace.ClassNames,
      customAddItemText: inputMultipleTranslations.invalid_item,
      duplicateItemsAllowed: inputMultipleConfig.duplicate_items_allowed,
      editItems: inputMultipleConfig.edit_items,
      filter,
      placeholder: inputMultipleConfig.placeholder,
      placeholderValue: placeholder,
      uniqueItemText: inputMultipleTranslations.unique_item,
      removeItemButton: inputMultipleConfig.remove_item_button,
    };

    this.initInputMultiple(this.element, this.config);
  }
  
  handlePlaceholder(choicesInput: Choices, inputElement: HTMLElement, placeholder = '') {
    const element = inputElement;

    if (choicesInput.getValue().length === 0) {
      element.setAttribute('placeholder', placeholder);
      element.style.width = '100%';
    } else {
      element.removeAttribute('placeholder');
    }
  }

  getAddItemText(value: string) {
    return (`${this.config.addItemMessage} <b>${value}</b>`);
  }

  addItemFilter(value: string) {
    if (!value) {
      return false;
    }

    const expression = new RegExp(this.config.filter, 'i');

    return expression.test(value.toLowerCase());
  }

  initInputMultiple(inputMultiple: HTMLInputElement | HTMLSelectElement, widgetConfig: Partial<ChoicesNamespace.Options>) {
    const choicesInput = new Choices(inputMultiple, widgetConfig);
    const inputElement = (choicesInput as any).input.element;

    choicesInput.passedElement.element.addEventListener('addItem', () => this.handlePlaceholder(choicesInput, inputElement), false);
    choicesInput.passedElement.element.addEventListener(
      'removeItem', () => this.handlePlaceholder(choicesInput, inputElement, widgetConfig.placeholderValue || ''), false,
    );
  } 
}
