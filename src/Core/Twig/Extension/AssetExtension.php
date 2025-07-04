<?php

namespace WS\Core\Twig\Extension;

use Twig\Attribute\AsTwigFunction;
use WS\Core\Entity\AssetFile;
use WS\Core\Entity\AssetImage;
use WS\Core\Service\FileService;
use WS\Core\Service\ImageService;

class AssetExtension
{
    public function __construct(
        protected ImageService $imageService,
        protected FileService $fileService
    ) {
    }

    #[AsTwigFunction(name: 'asset_get_image')]
    public function getImage(AssetImage $image, string $rendition, ?string $subRendition = null): string
    {
        return $this->imageService->getImageUrl($image, $rendition, $subRendition);
    }

    #[AsTwigFunction(name: 'asset_get_file')]
    public function getFile(AssetFile $file): string
    {
        return $this->fileService->getFileUrl($file);
    }
}
