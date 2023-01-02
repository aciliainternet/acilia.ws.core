import { Application } from '@hotwired/stimulus';
import WSSelect from './controllers/ws-select_controller';
import WSSlug from './controllers/ws-slug_controller';
import ModuleTranslationSidebar from './controllers/module-translation-sidebar_controller';
import Translation from './controllers/translation_controller';

// TODO: Use lazy loading with webpack or a similar feature
const stimulus = Application.start();
stimulus.register('ws-select', WSSelect);
stimulus.register('ws-slug', WSSlug);
stimulus.register('module-translation-sidebar', ModuleTranslationSidebar);
stimulus.register('translation', Translation);
