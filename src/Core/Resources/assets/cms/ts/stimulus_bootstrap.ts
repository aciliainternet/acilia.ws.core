import { Application } from '@hotwired/stimulus';
import WSSlug from './controllers/ws-slug_controller';
import WSSelect from './controllers/ws-select_controller';

const stimulus = Application.start();
stimulus.register('ws-slug', WSSlug);
stimulus.register('ws-select', WSSelect);

