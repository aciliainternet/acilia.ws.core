import Choices, { Choices as ChoicesNamespace } from 'choices.js';

export type Item = ChoicesNamespace.Item;

interface CurrentState {
  items: ChoicesNamespace.Item[];
  choices: ChoicesNamespace.Item[];
}

/**
 * Extending the Choices type due to bad typings
 * provided by the package.
 */
interface ChoicesExtended extends Choices {
  _currentState: CurrentState;
  choices: ChoicesNamespace.Item[];
  input: HTMLSelectElement | HTMLInputElement;
  clearData: () => void;
}

export type aSelectType = ChoicesExtended;

function init(
  elm: string | HTMLInputElement | HTMLSelectElement,
  options?: Partial<ChoicesNamespace.Options>
) {
  return new Choices(elm, options) as ChoicesExtended;
}

export default init;
