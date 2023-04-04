// turbo
import * as Turbo from '@hotwired/turbo';

// stimulus
import './stimulus_bootstrap.ts';

Turbo.start();

document.addEventListener('turbo:load', (event) => {
  console.log(event);
});
