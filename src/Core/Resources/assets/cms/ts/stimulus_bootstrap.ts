import { Application } from '@hotwired/stimulus';
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

// TODO: Use lazy loading with webpack or a similar feature
const stimulus = Application.start();
stimulus.register('module-translation-sidebar', ModuleTranslationSidebar);
stimulus.register('module-generic-delete', ModuleGenericDelete);
stimulus.register('module-batch-actions', ModuleBatchActions);
stimulus.register('module-filter', ModuleFilter);
stimulus.register('settings', Settings);
stimulus.register('sidebar', Sidebar);
stimulus.register('ws-select', WSSelect);
stimulus.register('ws-slug', WSSlug);
stimulus.register('translation', Translation);
stimulus.register('ws-tooltip', WSTooltip);
stimulus.register('ws-tabs', WSTabs);
stimulus.register('ws-datepicker', WSDatePicker);
stimulus.register('ws-input-multiple', WSInputMultiple);
stimulus.register('ws-widget-list-modal', WSWidgetListModal);
stimulus.register('ws-color-picker', WSColorPicker);
stimulus.register('ws-range-slider', WSRangeSlider);
stimulus.register('ws-dropdown', WSDropdown);
stimulus.register('ws-table-collapse', WSTableCollapse);
stimulus.register('ws-toggle-choice', WSToggleChoice);
