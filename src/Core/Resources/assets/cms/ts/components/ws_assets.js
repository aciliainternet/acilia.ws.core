function deleteAsset(event) {
  const { idAssetComponent } = event.currentTarget.dataset;
  if (idAssetComponent) {
    document.getElementById(`${idAssetComponent}_asset_remove`).value = 'remove';
    document.querySelector(`[data-id="${idAssetComponent}_link"]`).classList.add('u-hidden');
    document.querySelector(`.js-form-upload[data-id-asset-component="${idAssetComponent}"]`)
      .classList.remove('u-hidden');
  }
}

function init() {
  const { cmsTranslations } = window;

  if (cmsTranslations === undefined || cmsTranslations === null) {
    throw Error('No CMS Translations defined.');
  }

  document.querySelectorAll('.js-delete-asset').forEach((element) => {
    element.addEventListener('click', deleteAsset);
  });
}

export default init;
