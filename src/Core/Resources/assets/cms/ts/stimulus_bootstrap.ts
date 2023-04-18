import { Application } from '@hotwired/stimulus';
import './typings/global.d';

import ModuleTranslationSidebar from './controllers/module-translation-sidebar_controller';
import ModuleGenericDelete from './controllers/module-generic-delete_controller';
import ModuleBatchActions from './controllers/module-batch-actions_controller';
import ModuleFilter from './controllers/module-filter_controller';
import Settings from './controllers/settings_controller';
import Sidebar from './controllers/sidebar_controller';
import WSSelect from './controllers/ws-select_controller';
import WSSlug from './controllers/ws-slug_controller';
import Translation from './controllers/translation_controller';
import WSTooltip from './controllers/ws-tooltip_controller';
import WSTabs from './controllers/ws-tabs_controller';
import WSDatePicker from './controllers/ws-date-picker_controller';
import WSInputMultiple from './controllers/ws-input-multiple_controller';
import WSWidgetListModal from './controllers/ws-widget-list-modal_controller';
import WSColorPicker from './controllers/ws-color-picker_controller';
import WSRangeSlider from './controllers/ws-range-slider_controller';
import WSDropdown from './controllers/ws-dropdown_controller';
import WSTableCollapse from './controllers/ws-table-collapse_controller';
import WSToggleChoice from './controllers/ws-toggle-choice_controller';
import WSAsset from './controllers/ws-asset_controller';
import WSMarkdown from './controllers/ws-markdown_controller';
import WSMarkdownFile from './controllers/ws_markdown/ws-markdown-file_controller';
import WSMarkdownImage from './controllers/ws_markdown/ws-markdown-image_controller';
import WSAssetImage from './controllers/ws-asset-image_controller';
import WSNotifications from './controllers/notifications_controller';
import WSTurboVisit from './controllers/ws-turbo-visit_controller';

// TODO: Use lazy loading with webpack or a similar feature
if (window.Stimulus === undefined) {
  window.Stimulus = Application.start();
}

window.Stimulus.register(
  'module-translation-sidebar',
  ModuleTranslationSidebar
);
window.Stimulus.register('module-generic-delete', ModuleGenericDelete);
window.Stimulus.register('module-batch-actions', ModuleBatchActions);
window.Stimulus.register('module-filter', ModuleFilter);
window.Stimulus.register('notifications', WSNotifications);
window.Stimulus.register('settings', Settings);
window.Stimulus.register('sidebar', Sidebar);
window.Stimulus.register('translation', Translation);
window.Stimulus.register('ws-select', WSSelect);
window.Stimulus.register('ws-slug', WSSlug);
window.Stimulus.register('ws-tooltip', WSTooltip);
window.Stimulus.register('ws-tabs', WSTabs);
window.Stimulus.register('ws-datepicker', WSDatePicker);
window.Stimulus.register('ws-input-multiple', WSInputMultiple);
window.Stimulus.register('ws-widget-list-modal', WSWidgetListModal);
window.Stimulus.register('ws-color-picker', WSColorPicker);
window.Stimulus.register('ws-range-slider', WSRangeSlider);
window.Stimulus.register('ws-dropdown', WSDropdown);
window.Stimulus.register('ws-table-collapse', WSTableCollapse);
window.Stimulus.register('ws-toggle-choice', WSToggleChoice);
window.Stimulus.register('ws-asset', WSAsset);
window.Stimulus.register('ws-markdown', WSMarkdown);
window.Stimulus.register('ws-markdown-file', WSMarkdownFile);
window.Stimulus.register('ws-markdown-image', WSMarkdownImage);
window.Stimulus.register('ws-asset-image', WSAssetImage);
window.Stimulus.register('ws-turbo-visit', WSTurboVisit);
