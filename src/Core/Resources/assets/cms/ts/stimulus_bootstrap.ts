import { Application } from '@hotwired/stimulus';
import WSSlug from './controllers/ws-slug_controller';
import WSSelect from './controllers/ws-select_controller';
import WSDatePicker from './controllers/ws-date-picker_controller';
import WSInputMultiple from './controllers/ws-input-multiple_controller';

const stimulus = Application.start();
stimulus.register('ws-slug', WSSlug);
stimulus.register('ws-select', WSSelect);
stimulus.register('ws-datepicker', WSDatePicker);
stimulus.register('ws-input-multiple', WSInputMultiple);
