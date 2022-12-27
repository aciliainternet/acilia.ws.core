<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Entity\AssetImage;
use WS\Core\Service\ImageService;

class AssetExtension extends AbstractExtension
{
    public function __construct(protected ImageService $imageService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_get_image', [$this, 'getImage']),
        ];
    }

    public function getImage(AssetImage $image, string $rendition, ?string $subRendition = null): string
    {
        return $this->imageService->getImageUrl($image, $rendition, $subRendition);
    }
}
