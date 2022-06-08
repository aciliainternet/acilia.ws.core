/* eslint-disable no-underscore-dangle */
import aSelect from '../modules/a_select';

let selectTranslations = null;
const fetchLookupDelay = 500;
let fetchLookupTimeout = null;
const fetchLookupCache = {};

function populateChoices(choices, items) {
  const toRemove = choices._currentState.items.filter((item) => item.active);
  const toKeep = items.filter((item) => !toRemove.includes(item.id));

  choices.setChoices(toKeep, 'value', 'label', true);
}

async function lookup(choices, apiUrl) {
  // show temporary loading option
  choices.clearChoices();
  choices.setChoices([{ value: 0, label: selectTranslations.loading }, 'value', 'label', true]);

  const query = choices.input.value;

  // check if it is cached
  if (query in fetchLookupCache) {
    populateChoices(choices, fetchLookupCache[query]);
  } else {
    // not cached, let's fetch the API
    const url = `${apiUrl}?query=${query}`;
    try {
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (response.ok) {
        const data = await response.json();
        populateChoices(choices, data);
      } else {
        choices.clearData();
      }
    } catch (e) {
      choices.clearData();
    }
  }
}

function setUpLookup(elm, choices) {
  // reduce select items
  if (choices._currentState.choices.length > 100) {
    const slicedChoices = choices._currentState.choices.slice(0, 100);
    choices.setChoices(slicedChoices, 'value', 'label', true);
  }

  // trigger API lookup when the user stops typing
  elm.addEventListener('search', () => {
    clearTimeout(fetchLookupTimeout);
    fetchLookupTimeout = setTimeout(() => lookup(choices, elm.dataset.lookup), fetchLookupDelay);
  });
}

function init() {
  const { cmsTranslations } = window;
  if (cmsTranslations === undefined || cmsTranslations === null) {
    throw Error('No CMS Translations defined.');
  }

  selectTranslations = cmsTranslations.ws_cms_components.select;
  const config = {
    loadingText: selectTranslations.loading,
    noResultsText: selectTranslations.no_results,
    noChoicesText: selectTranslations.no_choices,
    itemSelectText: selectTranslations.item_select,
    removeItems: true,
    removeItemButton: true,
    resetScrollPosition: false,
  };

  document.querySelectorAll('[data-component="ws_select"]').forEach((elm) => {
    if (!elm.dataset.wsDisable) {
      config.searchEnabled = elm.dataset.search ? elm.dataset.search : false;
      config.searchResultLimit = elm.dataset.searchLimit ? elm.dataset.searchLimit : 9999;
      const choices = aSelect(elm, config);

      if (elm.dataset.search && elm.dataset.lookup) {
        setUpLookup(elm, choices);
      }
    }
  });
}

export default init;
