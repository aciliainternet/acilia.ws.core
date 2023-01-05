// Polyfills... because EDGE 15.
import 'core-js/features/dom-collections/for-each';

// components
import componentMarkdown from './components/ws_markdown';
import componentInputMultiple from './components/ws_input_multiple';
import componentWidgetListModal from './components/ws_widget_list_modal';
import componentAssetsImage from './components/ws_assets_image';
import componentAssets from './components/ws_assets';
import componentColorPicker from './components/ws_colorPicker';
import componentRangeSlider from './components/ws_rangeSlider';
import componentDropdown from './components/ws_dropdown';
import componentTableCollapse from './components/ws_table_collapse';
import componentToggleChoice from './components/ws_toggle_choice';

// stimulus
import '../ts/stimulus_bootstrap.ts';

componentMarkdown();
componentInputMultiple();
componentWidgetListModal();
componentAssetsImage();
componentAssets();
componentColorPicker();
componentRangeSlider();
componentDropdown();
componentTableCollapse();
componentToggleChoice();
