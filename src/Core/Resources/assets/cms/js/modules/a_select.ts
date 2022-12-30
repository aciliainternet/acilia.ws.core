import Choices, { Choices as ChoicesNamespace } from 'choices.js';

export type aSelectType = Choices;

function init(
  elm: string | HTMLInputElement | HTMLSelectElement,
  options?: Partial<ChoicesNamespace.Options>
) {
  return new Choices(elm, options);
}

export default init;
