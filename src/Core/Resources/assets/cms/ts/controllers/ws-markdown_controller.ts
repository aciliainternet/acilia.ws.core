// add this line because eslint demands that the package be on dependencies
// eslint-disable-next-line import/no-extraneous-dependencies
import EasyMDE from 'easymde';
import '../typings/global.d';
import { Controller } from '@hotwired/stimulus';
import { handleImage } from './ws_markdown/ws-markdown-image_controller';
import { handleFile } from './ws_markdown/ws-markdown-file_controller';
import { CMSTranslations } from '../interfaces/translations';

interface EditorInstance {
  key: HTMLElement;
  instance: EasyMDE;
}

const editors: EditorInstance[] = [];
 
export default class extends Controller<HTMLElement> { 
  static targets = ['textarea', 'filePlugin', 'imagePlugin'];

  declare textareaTarget: HTMLTextAreaElement;
  declare filePluginTarget: HTMLElement;
  declare imagePluginTarget: HTMLElement;

  connect() {
    const { cmsTranslations } = window;

    if (cmsTranslations === undefined || cmsTranslations === null) {
      throw Error('No CMS Translations defined.');
    }

    editors.push({
      key: this.textareaTarget,
      instance: this.createMarkdown(this.textareaTarget, cmsTranslations, this.getConfig()),
    });
  }

  clearLocalStorageData(id: string) {
    const { localStorage, performance } = window;

    if (performance.navigation.type !== performance.navigation.TYPE_RELOAD) {
      Object.keys(localStorage).forEach((key) => {
        if (key.includes(id)) {
          localStorage.removeItem(key);
        }
      });
    }
  }

  getConfig(): EasyMDE.Options {
    return {
      status: ['autosave', 'lines', 'words', 'cursor'],
      autosave: {
        uniqueId: '',
        enabled: true,
      },
      spellChecker: false,
      nativeSpellcheck: true,
      previewRender: () => null,
      autoDownloadFontAwesome: false,
      hideIcons: ['image', 'side-by-side'],
      toolbar: [
        'bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', 'link', 'preview',
        {
          name: 'Insert Image',
          action: (editor) => {
            this.imagePluginTarget.click();
            handleImage().then((image) => {
              editor.codemirror.replaceSelection(`![${image.name}](${image.path})`);
            });
          },
          className: 'fa fa-image',
          title: 'Insert Image',
        },
        {
          name: 'Insert File',
          action: (editor) => {
            this.filePluginTarget.click();
            handleFile().then((file) => {
              editor.codemirror.replaceSelection(`[${file.name}](${file.path})`);
            });
          },
          className: 'fa fa-file',
          title: 'Insert File',
        },
      ],
    };
  }

  createMarkdown(elm: HTMLElement, cmsTranslations: CMSTranslations, config: EasyMDE.Options) {
    const mdeConfiguration = config;
    mdeConfiguration.element = elm;

    // before autosave, we clear localstorage
    this.clearLocalStorageData(elm.id);

    if (mdeConfiguration.autosave) {
      mdeConfiguration.autosave.uniqueId = elm.id;
      mdeConfiguration.autosave.text = cmsTranslations.ws_cms_components.markdown.autosave;
    }

    return new EasyMDE(mdeConfiguration);
  }
}

export function refreshEditor(id: HTMLElement) {
  const editor = editors.find((e) => e.key === id);
  if (editor) {
    editor.instance.codemirror.refresh();
  }
}