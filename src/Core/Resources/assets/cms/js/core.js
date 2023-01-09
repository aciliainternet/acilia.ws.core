// Polyfills... because EDGE 15.
import 'core-js/features/dom-collections/for-each';

// components
import componentMarkdown from './components/ws_markdown';
import componentAssetsImage from './components/ws_assets_image';
import componentAssets from './components/ws_assets';

// stimulus
import '../ts/stimulus_bootstrap.ts';

componentMarkdown();
componentAssetsImage();
componentAssets();
