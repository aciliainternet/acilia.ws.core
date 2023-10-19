<?php

namespace WS\Core\Library\Traits\CRUD;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WS\Core\Entity\AssetFile;
use WS\Core\Entity\AssetImage;

trait AssetTrait
{
    protected function handleImages(FormInterface $form, object $entity): void
    {
        foreach ($this->getService()->getImageFields($form, $entity) as $imageField) {
            if (!empty($form->get($imageField)->get('asset')->getData())) {
                /** @var UploadedFile */
                $imageFile = $form->get($imageField)->get('asset')->getData();

                /** @var array */
                $formFieldOptions = $form->get($imageField)->getConfig()->getOptions();
                $options = [
                    'cropper' => $form->get($imageField)->get('cropper')->getData(),
                    'thumb-rendition' => $formFieldOptions['ws']['thumb-rendition'] ?? null
                ];

                // Save asset image
                $assetImage = $this->imageService->handle(
                    $entity,
                    $imageField,
                    $imageFile,
                    $options,
                    $this->getService()->getImageEntityClass($entity)
                );

                // Hanlde asset image post save
                $this->handleImageField($entity, $imageField, $assetImage);
            } elseif (
                $form->get($imageField)->has('asset_data') &&
                is_numeric($form->get($imageField)->get('asset_data')->getData())
            ) {
                $imageId = intval($form->get($imageField)->get('asset_data')->getData());

                $options = [
                    'cropper' => $form->get($imageField)->get('cropper')->getData()
                ];

                // Copy asset image
                $assetImage = $this->imageService->copy(
                    $entity,
                    $imageField,
                    $imageId,
                    $options,
                    $this->getService()->getImageEntityClass($entity)
                );

                // Hanlde asset image post copy
                $this->handleImageField($entity, $imageField, $assetImage);
            } elseif (
                $form->get($imageField)->has('asset_remove')
                && $form->get($imageField)->get('asset_remove')->getData() === 'remove'
            ) {
                // Delete asset image
                $this->imageService->delete($entity, $imageField);

                // Hanlde asset image post delete
                $this->handleImageField($entity, $imageField, null);
            }
        }

        $this->doctrine->flush();
    }

    protected function handleFiles(FormInterface $form, object $entity): void
    {
        foreach ($this->getService()->getFileFields($form, $entity) as $fileField) {
            if (!empty($form->get($fileField)->get('asset')->getData())) {
                /** @var UploadedFile */
                $fileFile = $form->get($fileField)->get('asset')->getData();

                /** @var array */
                $formFieldOptions = $form->get($fileField)->getConfig()->getOptions();
                $options = [
                    'context' => $formFieldOptions['ws']['context'] ?? null
                ];

                // Save asset file
                $assetFile = $this->fileService->handle($fileFile, $entity, $fileField, $options);

                // Hanlde asset file post save
                $this->handleFileField($entity, $fileField, $assetFile);
            } elseif (
                $form->get($fileField)->has('asset_remove') &&
                $form->get($fileField)->get('asset_remove')->getData() === 'remove'
            ) {
                // Delete asset file
                $this->fileService->delete($entity, $fileField);

                // Hanlde asset file post delete
                $this->handleFileField($entity, $fileField, null);
            }
        }

        $this->doctrine->flush();
    }

    protected function handleImageField(object $entity, string $imageField, ?AssetImage $assetImage): void
    {
    }

    protected function handleFileField(object $entity, string $fileFile, ?AssetFile $assetFile): void
    {
    }
}
