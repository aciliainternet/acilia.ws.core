function toggleFilter() {
  if(document.querySelector('.js-toggle-filter-row').classList.contains('u-hidden')) {
    document.querySelector('.js-toggle-filter-row').classList.remove('u-hidden');
  } else {
    document.querySelector('.js-toggle-filter-row').classList.add('u-hidden');
  }
}

function init() {
  if(document.querySelector('.js-toggle-filter')) {
    document.querySelector('.js-toggle-filter').addEventListener('click', toggleFilter);
  }
}

export default init();
