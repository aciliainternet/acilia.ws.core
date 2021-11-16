<?php

namespace WS\Core\Library\Asset;

use WS\Core\Service\ImageService;

trait ImageConsumerTrait
{
    protected ImageService $imageService;

    public function setImageService(ImageService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function getImageService(): ImageService
    {
        return $this->imageService;
    }
}
