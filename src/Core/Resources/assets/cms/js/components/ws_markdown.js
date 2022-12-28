// add this line because eslint demands that the package be on dependencies
// eslint-disable-next-line import/no-extraneous-dependencies
import EasyMDE from 'easymde';
import { init as initMarkdownImage, handleImage } from './ws_markdown/ws_markdown_image';
import { init as initMarkdownFile, handleFile } from './ws_markdown/ws_markdown_file';

const editors = [];

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
      {
        name: 'Insert File',
        action: function addFile(editor) {
          document.querySelector('[data-component="ws_markdown_file"]').click();
          handleFile().then((file) => {
            editor.codemirror.replaceSelection(`![${file.name}](${file.path})`);
          });
        },
        className: 'fa fa-document',
        title: 'Insert File',
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

export function refreshEditor(id) {
  const editor = editors.find((e) => e.key === id);
  if (editor) {
    editor.instance.codemirror.refresh();
  }
}

function init() {
  const markDowns = document.querySelectorAll('[data-component="ws_markdown"]');

  if (markDowns.length > 0) {
    const { cmsTranslations } = window;

    if (cmsTranslations === undefined || cmsTranslations === null) {
      throw Error('No CMS Translations defined.');
    }

    markDowns.forEach((elm) => {
      editors.push({key: elm, instance: createMarkdown(elm, cmsTranslations, getConfig())});
    });

    initMarkdownImage();
  }
}

export default init;
