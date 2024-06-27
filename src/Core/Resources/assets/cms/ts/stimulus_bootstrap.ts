import { Application } from '@hotwired/stimulus';
import WSSlug from './controllers/ws-slug_controller';
import WSSelect from './controllers/ws-select_controller';
import WSDatePicker from './controllers/ws-date-picker_controller';
import WSInputMultiple from './controllers/ws-input-multiple_controller';
import WSColorPicker from './controllers/ws-color-picker_controller';
import WSRangeSlider from './controllers/ws-range-slider_controller';


const stimulus = Application.start();
stimulus.register('ws-slug', WSSlug);
stimulus.register('ws-select', WSSelect);
stimulus.register('ws-datepicker', WSDatePicker);
stimulus.register('ws-input-multiple', WSInputMultiple);
stimulus.register('ws-color-picker', WSColorPicker);
stimulus.register('ws-range-slider', WSRangeSlider);
