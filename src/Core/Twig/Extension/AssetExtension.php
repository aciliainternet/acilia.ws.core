<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Entity\AssetFile;
use WS\Core\Entity\AssetImage;
use WS\Core\Service\FileService;
use WS\Core\Service\ImageService;

class AssetExtension extends AbstractExtension
{
    public function __construct(
        protected ImageService $imageService,
        protected FileService $fileService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_get_image', [$this, 'getImage']),
            new TwigFunction('asset_get_file', [$this, 'getFile']),
        ];
    }

    public function getImage(AssetImage $image, string $rendition, ?string $subRendition = null): string
    {
        return $this->imageService->getImageUrl($image, $rendition, $subRendition);
    }

    public function getFile(AssetFile $file): string
    {
        return $this->fileService->getFileUrl($file);
    }
}
