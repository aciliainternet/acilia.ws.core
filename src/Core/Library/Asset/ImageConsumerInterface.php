<?php

namespace WS\Core\Library\Asset;

use WS\Core\Service\ImageService;

interface ImageConsumerInterface
{
    public function setImageService(ImageService $imageService): void;

    public function getImageService(): ImageService;
}
