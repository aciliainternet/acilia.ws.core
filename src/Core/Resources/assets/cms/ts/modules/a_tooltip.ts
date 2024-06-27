import tippy, { MultipleTargets, Props } from 'tippy.js';

function init(elements: MultipleTargets, config: Partial<Props>) {
  tippy(elements, config);
}

export default init;
