import Choices, { Choices as ChoicesNamespace } from 'choices.js';

export interface Item {
  id: string;
  active: boolean;
}

interface CurrentState {
  items: Item[];
  choices: Item[];
}

/**
 * Extending the Choices type due to bad typings
 * provided by the package.
 */
interface ChoicesExtended extends Choices {
  _currentState: CurrentState;
  choices: Item[];
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
