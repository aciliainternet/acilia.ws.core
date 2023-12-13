<?php

namespace WS\Core\Twig\Extension;

use WS\Core\Service\MetadataConsumerService;
use WS\Core\Twig\Tag\MetaTags\MetaTagsTokenParser;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

class MetadataExtension extends AbstractExtension
{
    public function __construct(private MetadataConsumerService $metadataConsumerService)
    {
    }

    public function getTokenParsers(): array
    {
        return [new MetaTagsTokenParser()];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_metatags', [$this, 'renderMetaTags'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }


    public function renderMetaTags(Environment $environment): string
    {
        try {
            return $environment->render('@WSCore/metatags/metas.html.twig', [
                'metas' => $this->metadataConsumerService->compile(),
                'custom' => $this->metadataConsumerService->getCustomTags()
            ]);
        } catch (\Exception) {
        }

        return '';
    }

    public function register(array $metas): void
    {
        $this->metadataConsumerService->register($metas, $metas['order'] ?? 10);
    }
}
