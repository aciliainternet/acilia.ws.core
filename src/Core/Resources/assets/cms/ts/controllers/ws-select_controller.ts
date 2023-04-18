import '../typings/global.d';
import { Controller } from '@hotwired/stimulus';
/* eslint-disable no-underscore-dangle */
import aSelect, { ChoicesExtended, Item } from '../modules/a_select';
import { SelectTranslations } from '../interfaces/translations';

let selectTranslations: SelectTranslations | null = null;
const fetchLookupDelay = 500;
let fetchLookupTimeout: number | null = null;
const fetchLookupCache: { [index: string]: Item[] } = {};

interface WSSelectConfig {
  loadingText: string;
  noResultsText: string;
  noChoicesText: string;
  itemSelectText: string;
  removeItems: boolean;
  removeItemButton: boolean;
  resetScrollPosition: boolean;
  searchEnabled?: boolean;
  searchResultLimit?: number;
}

export default class extends Controller<HTMLInputElement | HTMLSelectElement> {
  connect() {
    const { cmsTranslations } = window;
    if (cmsTranslations === undefined || cmsTranslations === null) {
      throw Error('No CMS Translations defined.');
    }

    selectTranslations = cmsTranslations.ws_cms_components.select;

    const config: WSSelectConfig = {
      loadingText: selectTranslations.loading,
      noResultsText: selectTranslations.no_results,
      noChoicesText: selectTranslations.no_choices,
      itemSelectText: selectTranslations.item_select,
      removeItems: true,
      removeItemButton: true,
      resetScrollPosition: false,
    };

    if (!this.element.dataset.wsDisable) {
      config.searchEnabled = this.element.dataset.search
        ? this.element.dataset.search === 'true'
        : false;

      config.searchResultLimit = this.element.dataset.searchLimit
        ? Number(this.element.dataset.searchLimit)
        : 9999;

      const choices = aSelect(this.element, config);

      if (this.element.dataset.search && this.element.dataset.lookup) {
        this.setUpLookup(this.element, choices as ChoicesExtended);
      }
    }
  }

  populateChoices(choices: ChoicesExtended, items: Item[]) {
    const toRemove = choices._currentState.items
      .filter((item) => item.active)
      .map((item) => item.id);

    const toKeep = items.filter((item) => !toRemove.includes(item.id));

    choices.setChoices(toKeep, 'value', 'label', true);
  }

  async lookup(choices: ChoicesExtended, apiUrl: string) {
    // show temporary loading option
    choices.clearChoices();
    choices.setChoices(
      [{ value: 0, label: selectTranslations?.loading }],
      'value',
      'label',
      true
    );

    const query = choices.input.value;

    // check if it is cached
    if (query in fetchLookupCache) {
      this.populateChoices(choices, fetchLookupCache[query]);
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
          this.populateChoices(choices, data);
        } else {
          choices.clearData();
        }
      } catch (e) {
        choices.clearData();
      }
    }
  }

  setUpLookup(
    elm: HTMLInputElement | HTMLSelectElement,
    choices: ChoicesExtended
  ) {
    // reduce select items
    if (choices._currentState.choices.length > 100) {
      const slicedChoices = choices._currentState.choices.slice(0, 100);
      choices.setChoices(slicedChoices, 'value', 'label', true);
    }

    // trigger API lookup when the user stops typing
    elm.addEventListener('search', () => {
      if (fetchLookupTimeout !== null) {
        window.clearTimeout(fetchLookupTimeout);
      }

      fetchLookupTimeout = window.setTimeout(
        () => this.lookup(choices, elm.dataset.lookup || ''),
        fetchLookupDelay
      );
    });
  }
}
