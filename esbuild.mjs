/* eslint-disable quotes */
import * as dotenv from 'dotenv';
import { build } from 'esbuild';
import manifestPlugin from 'esbuild-plugin-manifest';
import { sassPlugin } from 'esbuild-sass-plugin';
import { cleanPlugin } from 'esbuild-clean-plugin';
import copy from 'esbuild-copy-files-plugin';

dotenv.config();

function generateManifest(entry) {
  const manifestKeysMap = {
    'core.scss': 'core.css',
    'core.js': 'core.js',
  };

  const manifestEntry = entry;
  return Object.keys(manifestEntry).reduce((acc, key) => {
    manifestEntry[key] = manifestEntry[key].replace('src/Core/Resources/public/', '/bundles/wscore/');
    return { ...acc, [manifestKeysMap[key]]: manifestEntry[key] };
  }, {});
}

const isWatch = process.argv.includes('--watch');
const isDev = isWatch || process.env.APP_ENV === 'dev';
const entryPoints = [
  'src/Core/Resources/assets/cms/css/core.scss',
  'src/Core/Resources/assets/cms/js/core.js',
];

build({
  entryPoints,
  metafile: true,
  bundle: true,
  minify: !isDev,
  sourcemap: isDev,
  format: 'iife',
  treeShaking: true,
  logLevel: 'info',
  watch: isWatch,
  target: ['es2018'],
  outbase: 'src/Core/Resources/assets/cms',
  outdir: 'src/Core/Resources/public',
  loader: {
    '.eot': 'file',
    '.ttf': 'file',
    '.png': 'file',
    '.webp': 'file',
    '.woff': 'file',
    '.woff2': 'file',
    '.svg': 'file',
  },
  assetNames: '[dir]/[name]',
  plugins: [
    cleanPlugin({
      initialCleanPatterns: ['**/*'],
    }),
    {
      name: 'resolveFonts',
      setup(bld) {
        // Mark all paths starting with "../fonts/" as external
        bld.onResolve(
          { filter: /^\.\.\/fonts\// },
          (args) => ({ path: args.path, external: true }),
        );
      },
    },
    sassPlugin(),
    manifestPlugin({
      shortNames: 'input',
      generate: generateManifest,
    }),
    copy({
      source: './src/Core/Resources/assets/cms/images',
      target: './src/Core/Resources/public/images',
      copyWithFolder: false,
    }),
    copy({
      source: [
        './src/Core/Resources/assets/cms/fonts',
        './node_modules/@fontsource/ibm-plex-sans/files',
      ],
      target: './src/Core/Resources/public/fonts',
      copyWithFolder: false,
    }),
  ],
});
