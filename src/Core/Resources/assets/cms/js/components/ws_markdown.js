import EasyMDE from "easymde";
// eslint-disable
import { convertHtmlToMarkdown } from "./ws_html_to_markdown";
import {
  init as initMarkdownImage,
  handleImage,
} from "./ws_markdown/ws_markdown_image";
import {
  init as initMarkdownFile,
  handleFile,
} from "./ws_markdown/ws_markdown_file";
// eslint-enable

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
    status: false,
    autosave: {
      enabled: true,
    },
    spellChecker: false,
    nativeSpellcheck: true,
    previewRender: false,
    autoDownloadFontAwesome: false,
    minHeight: "100px",
    hideIcons: ["image", "side-by-side"],
    toolbar: [
      "bold",
      "italic",
      "heading",
      "quote",
      "code",
      "|",
      "unordered-list",
      "ordered-list",
      "|",
      "link",
      "preview",
      {
        name: "togglePreviewImages",
        action: function togglePreviewImages(editor) {
          editor.options.previewImagesInEditor =
            !editor.options.previewImagesInEditor;
          editor.codemirror.refresh();
        },
        className: "fa fa-images",
        title: "Toggle Image Preview",
      },
      {
        name: "Insert Image",
        action: function addImage(editor) {
          const fileInput = document.querySelector(
            '[data-component="ws_markdown_image"]'
          );
          fileInput.click();
          handleImage().then((image) => {
            editor.codemirror.replaceSelection(
              `![${image.name}](${image.path})`
            );
            fileInput.value = null;
          });
        },
        className: "fa fa-image",
        title: "Insert Image",
      },
      {
        name: "Insert File",
        action: function addFile(editor) {
          const fileInput = document.querySelector(
            '[data-component="ws_markdown_file"]'
          );
          fileInput.click();
          handleFile().then((file) => {
            editor.codemirror.replaceSelection(`[${file.name}](${file.path})`);
            fileInput.value = null;
          });
        },
        className: "fa fa-file",
        title: "Insert File",
      },
    ],
  };
}

function createMarkdown(elm, cmsTranslations, config) {
  const mdeConfiguration = config;
  mdeConfiguration.element = elm;

  clearLocalStorageData(elm.id);
  mdeConfiguration.autosave.uniqueId = elm.id;
  mdeConfiguration.autosave.text =
    cmsTranslations.ws_cms_components.markdown.autosave;

  const mde = new EasyMDE(mdeConfiguration);

  mde.codemirror.on("paste", function (codemirror, event) {
    event.preventDefault();
    const clipboardData = event.clipboardData || window.clipboardData;
    const pastedData = convertHtmlToMarkdown(
      clipboardData.getData("text/html")
    );
    let markdown;
    if (pastedData) {
      markdown = convertHtmlToMarkdown(pastedData);
    } else {
      markdown = clipboardData.getData("text/plain");
    }
    codemirror.replaceSelection(markdown);
  });

  return mde;
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
      throw Error("No CMS Translations defined.");
    }

    markDowns.forEach((elm) => {
      editors.push({
        key: elm,
        instance: createMarkdown(elm, cmsTranslations, getConfig()),
      });
    });

    initMarkdownImage();
    initMarkdownFile();
  }
}

export default init;
