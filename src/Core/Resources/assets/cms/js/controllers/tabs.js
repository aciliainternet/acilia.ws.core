// data-tabsection and data-tablink should have the same name, this data allow you create more than one tab control in the same page

function changeTab(event) {
  const tab = event.currentTarget;
  if (tab.classList.contains('is-active')) {
    return false;
  }

  const section = document.getElementById(tab.dataset.tab);

  tab.parentElement.querySelector('.is-active').classList.remove('is-active');
  tab.classList.add('is-active');

  document.querySelector(`[data-tablink=${tab.dataset.tabsection}].is-active`).classList.remove('is-active');
  section.classList.add('is-active');

  return true;
}

function init() {
  const tabs = document.querySelectorAll('[data-tab]');
  if (tabs) {
    tabs.forEach((el) => {
      el.addEventListener('click', changeTab);
    });
  }
}

export default init;
