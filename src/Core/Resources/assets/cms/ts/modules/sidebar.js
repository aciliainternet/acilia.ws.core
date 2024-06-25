import smoothscroll from 'smoothscroll-polyfill';

const sections = {};
let scrollPosition = 0;
let isScrolling;
let hasEventOnScroll = true;

function removeActive() {
  if (document.querySelector('.js-toc-link.is-active')) {
    document.querySelector('.js-toc-link.is-active').classList.remove('is-active');
  }
}

function makeActive(menuLink) {
  removeActive();

  if (menuLink) {
    menuLink.classList.add('is-active');
  }
}

function onScroll() {
  if (hasEventOnScroll) {
    const top = window.pageYOffset || document.documentElement.scrollTop;
    const isScrollDown = window.scrollY > scrollPosition;
    const listSections = isScrollDown ? Object.keys(sections) : Object.keys(sections).reverse();

    listSections.forEach((key) => {
      const { offsetTop, offsetHeight } = sections[key];

      const condition = isScrollDown
        ? offsetTop <= top
        : offsetTop + (offsetHeight / 2) > top;

      if (condition) {
        makeActive(document.querySelector(`.js-toc-link[data-menu-link=${key}]`));
      }
    });
  } else {
    // Control para cuando el scroll termina,
    // volver a habilitar los eventos en scroll que fueron deshabilitados cuando se hizo click en un link del menu
    window.clearTimeout(isScrolling);
    isScrolling = setTimeout(() => {
      hasEventOnScroll = true;
    }, 66);
  }

  scrollPosition = window.scrollY;
}

function goToBlock(event) {
  const { currentTarget } = event;
  const { menuLink } = currentTarget.dataset;
  const gap = 80;

  if (document.querySelector(`.js-block[data-menu-link=${menuLink}]`)) {
    hasEventOnScroll = false;
    makeActive(currentTarget);
    const { offsetTop } = document.querySelector(`.js-block[data-menu-link=${menuLink}]`);
    window.scroll({ top: offsetTop + gap, behavior: 'smooth' });
  }
}

function init() {
  if (document.querySelector('[data-component="translation-sidebar"]')) {
    smoothscroll.polyfill();
    document.querySelectorAll('.js-block').forEach((element) => {
      sections[element.dataset.menuLink] = {
        offsetTop: element.offsetTop,
        offsetHeight: element.offsetHeight,
      };
    });

    window.addEventListener('scroll', onScroll);

    document.querySelectorAll('.js-toc-link').forEach((link) => {
      link.addEventListener('click', goToBlock);
    });
  }
}

export default init();
