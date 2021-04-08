// Polyfills... because EDGE 15.
import 'core-js/features/dom-collections/for-each';

// modules
import './modules/genericDelete';
import './modules/batchActions';
import './modules/filter';
import { init as moduleNotifications } from './modules/a_notifications';

// components
import componentSlug from './components/ws_slug';
import componentSelect from './components/ws_select';
import componentMarkdown from './components/ws_markdown';
import componentDatePicker from './components/ws_datePicker';
import componentInputMultiple from './components/ws_input_multiple';
import componentWidgetListModal from './components/ws_widget_list_modal';
import componentAssetsImage from './components/ws_assets_image';
import componentAssets from './components/ws_assets';
import componentColorPicker from './components/ws_colorPicker';
import componentRangeSlider from './components/ws_rangeSlider';
import componentTooltip from './components/ws_tooltip';
import componentDropdown from './components/ws_dropdown';
import componentTableCollapse from './components/ws_table_collapse';
import componentToggleChoice from './components/ws_toggle_choice';

// controllers
import settingsCntrl from './controllers/settings';
import translationCntrl from './controllers/translation';
import sidebarCntrl from './controllers/sidebar';

sidebarCntrl();
moduleNotifications();
componentMarkdown();
componentDatePicker();
componentInputMultiple();
componentSlug();
componentSelect();
componentWidgetListModal();
componentAssetsImage();
componentAssets();
componentColorPicker();
componentRangeSlider();
componentTooltip();
componentDropdown();
componentTableCollapse();
componentToggleChoice();

if (document.querySelector('[data-page="settings"]')) {
  settingsCntrl();
}

if (document.querySelector('[data-page="translation"]')) {
  translationCntrl();
}
