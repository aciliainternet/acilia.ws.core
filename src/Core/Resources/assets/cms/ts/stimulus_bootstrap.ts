import { Application } from '@hotwired/stimulus';
import WSSelect from './controllers/ws-select_controller';

// TODO: Use lazy loading with webpack or a similar feature
const stimulus = Application.start();
stimulus.register('ws-select', WSSelect);
