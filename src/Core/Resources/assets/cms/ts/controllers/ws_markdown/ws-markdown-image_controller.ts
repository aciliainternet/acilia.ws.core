import { Controller } from '@hotwired/stimulus';
import { FileResponse } from '../../interfaces/markdown';

let newImage: FileResponse | null = null;

export default class extends Controller {
  async onChange(event: InputEvent) {
    const target = event.currentTarget as HTMLInputElement;
    if (target.files && target.files.length) {
      newImage = await this.getImage(target.files[0]);
    }
  }

  async getImage(file: File): Promise<FileResponse> {
    const formData = new FormData();
    formData.append('asset', file);

    const response = await fetch(window.cmsSettings.ws_cms_components.markdown_asset_image.endpoint, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData,
    });

    return await response.json() as FileResponse;
  }
}

export function handleImage(): Promise<FileResponse> {
  return new Promise(((resolve) => {
    const interval = window.setInterval(() => {
      const tmpImage = newImage;
      if (tmpImage != null) {
        newImage = null;
        window.clearInterval(interval);
        resolve(tmpImage);
      }
    }, 10);
  }));
}
