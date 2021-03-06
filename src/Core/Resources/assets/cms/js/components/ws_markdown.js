// add this line because eslint demands that the package be on dependencies
// eslint-disable-next-line import/no-extraneous-dependencies
import EasyMDE from 'easymde';
import { init as initMarkdownImage, handleImage } from './ws_markdown/ws_markdown_image';

function clearLocalStorageData(id) {
  const { localStorage, performance } = window;

  if (performance.navigation.type !== performance.navigation.TYPE_RELOAD) {
    Object.keys(localStorage).forEach((key) => {
      if (key.includes(id)) {
        localStorage.removeItem(key);
      }
    });
  }
}

function getConfig() {
  return {
    status: ['autosave', 'lines', 'words', 'cursor'],
    autosave: {
      enabled: true,
    },
    spellChecker: false,
    nativeSpellcheck: true,
    previewRender: false,
    autoDownloadFontAwesome: false,
    hideIcons: ['image', 'side-by-side'],
    toolbar: [
      'bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', 'link', 'preview',
      {
        name: 'Insert Image',
        action: function addImage(editor) {
          document.querySelector('[data-component="ws_markdown_image"]').click();
          handleImage().then((image) => {
            editor.codemirror.replaceSelection(`![${image.name}](${image.path})`);
          });
        },
        className: 'fa fa-image',
        title: 'Insert Image',
      },
    ],
  };
}

function createMarkdown(elm, cmsTranslations, config) {
  const mdeConfiguration = config;
  mdeConfiguration.element = elm;

  // before autosave, we clear localstorage
  clearLocalStorageData(elm.id);

  mdeConfiguration.autosave.uniqueId = elm.id;
  mdeConfiguration.autosave.text = cmsTranslations.ws_cms_components.markdown.autosave;

  return new EasyMDE(mdeConfiguration);
}

function init() {
  const markDowns = document.querySelectorAll('[data-component="ws_markdown"]');

  if (markDowns.length > 0) {
    const { cmsTranslations } = window;

    if (cmsTranslations === undefined || cmsTranslations === null) {
      throw Error('No CMS Translations defined.');
    }

    markDowns.forEach((elm) => {
      createMarkdown(elm, cmsTranslations, getConfig());
    });

    initMarkdownImage();
  }
}

export default init;
