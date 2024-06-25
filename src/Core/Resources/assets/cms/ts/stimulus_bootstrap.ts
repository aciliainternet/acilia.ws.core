import { Application } from '@hotwired/stimulus';
import WSSlug from './controllers/ws-slug_controller';

const stimulus = Application.start();

stimulus.register('ws-slug', WSSlug);
