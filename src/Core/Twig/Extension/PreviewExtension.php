<?php

namespace WS\Core\Twig\Extension;

use Twig\Attribute\AsTwigFunction;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\PreviewService;

class PreviewExtension
{
    public function __construct(
        private ContextInterface $context,
        private PreviewService $previewService
    ){
    }

    #[AsTwigFunction(name: 'ws_preview_enabled')]
    public function isPreviewEnabled(): bool
    {
        return $this->previewService->isEnabled();
    }

    #[AsTwigFunction(name: 'ws_preview_supported')]
    public function isPreviewSupported(object $entity): bool
    {
        try {
            return $this->previewService->isSupported((new \ReflectionClass($entity))->getName());
        } catch (\ReflectionException) {
            return false;
        }
    }

    #[AsTwigFunction(name: 'ws_preview_path')]
    public function getPreviewPath(object $entity, array $options = [], array $queryString = []): string
    {
        $domain = $this->context->getDomain();
        if (null === $domain) {
            throw new \RuntimeException();
        }

        return sprintf(
            '%s://%s%s?%s=%s%s',
            $this->previewService->getScheme(),
            str_replace(['http://', 'https://'], '', $this->previewService->getHost() ?? $domain->getHost()),
            $this->previewService->getPath($entity, $options),
            $this->previewService->getQuery(),
            $this->previewService->hash($options),
            empty($queryString) ? '' : '&' . http_build_query($queryString)
        );
    }

    #[AsTwigFunction(name: 'ws_preview_locales')]
    public function getPreviewLocales(): array
    {
        return $this->previewService->getLocales();
    }
}
