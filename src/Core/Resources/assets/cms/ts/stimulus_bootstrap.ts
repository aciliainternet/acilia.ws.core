import { Application } from '@hotwired/stimulus';
import ModuleTranslationSidebar from './controllers/module-translation-sidebar_controller';
import ModuleGenericDelete from './controllers/module-generic-delete_controller';
import ModuleBatchActions from './controllers/module-batch-actions_controller';
import ModuleFilter from './controllers/module-filter_controller';
import WSSelect from './controllers/ws-select_controller';
import WSSlug from './controllers/ws-slug_controller';
import Translation from './controllers/translation_controller';
import WSTooltip from './controllers/ws-tooltip_controller';
import WSTabs from './controllers/ws-tabs_controller';

// TODO: Use lazy loading with webpack or a similar feature
const stimulus = Application.start();
stimulus.register('module-translation-sidebar', ModuleTranslationSidebar);
stimulus.register('module-generic-delete', ModuleGenericDelete);
stimulus.register('module-batch-actions', ModuleBatchActions);
stimulus.register('module-filter', ModuleFilter);
stimulus.register('ws-select', WSSelect);
stimulus.register('ws-slug', WSSlug);
stimulus.register('translation', Translation);
stimulus.register('ws-tooltip', WSTooltip);
stimulus.register('ws-tabs', WSTabs);
