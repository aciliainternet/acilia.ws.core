interface ILoader {
  elementBehind: HTMLElement | null;
  show: (element: HTMLElement | null | undefined) => void;
  hide: () => void;
}

export const Loader: ILoader = {
  elementBehind: null,

  show(element?: HTMLElement | null) {
    if (element) {
      Loader.elementBehind = element;
      Loader.elementBehind = Loader.elementBehind.querySelector('.js-loader')
        ? Loader.elementBehind
        : Loader.elementBehind.parentElement;

      if (Loader.elementBehind) {
        Loader.elementBehind
          .querySelector('.js-loader')
          ?.classList.add('is-active');

        Loader.elementBehind.classList.add('no-scroll');
      }
    }
  },

  hide() {
    if (Loader.elementBehind) {
      Loader.elementBehind
        .querySelector('.js-loader')
        ?.classList.remove('is-active');

      Loader.elementBehind.classList.remove('no-scroll');
    }
  },
};
