import { Controller } from '@hotwired/stimulus';
import { FileResponse } from '../../interfaces/markdown';

let newFile: FileResponse | null = null;

export default class extends Controller<HTMLInputElement> {
  async onChange() {
    if (this.element.files && this.element.files.length) {
      newFile = await this.getFile(this.element.files[0]);
    }
  }

  async getFile(file: File): Promise<FileResponse> {
    const formData = new FormData();
    formData.append('asset', file);

    const response = await fetch(
      window.cmsSettings.ws_cms_components.markdown_asset_file.endpoint,
      {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
      }
    );

    return (await response.json()) as FileResponse;
  }
}

export function handleFile(): Promise<FileResponse> {
  return new Promise((resolve) => {
    const interval = window.setInterval(() => {
      const tmpFile = newFile;
      if (tmpFile != null) {
        newFile = null;
        window.clearInterval(interval);
        resolve(tmpFile);
      }
    }, 10);
  });
}
