declare class Picker {
  /* eslint-disable no-useless-constructor */
  /* eslint-disable @typescript-eslint/no-unused-vars */
  constructor(element: HTMLElement | null) {
    // NOOP
  }

  /* eslint-disable class-methods-use-this */
  onChange(color: PickerColor): void {
    // noop
  }
}

declare namespace Picker {
  export function onChange(color: PickerColor): void;
}

declare module 'vanilla-picker' {
  export = Picker;

  export interface PickerColor {
    hex: string;
    rgbaString: string;
  }
}
