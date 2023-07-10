<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\PreviewService;

class PreviewExtension extends AbstractExtension
{
    public function __construct(
        private ContextInterface $context,
        private PreviewService $previewService
    ){
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ws_preview_enabled', [$this, 'isEnabled']),
            new TwigFunction('ws_preview_path', [$this, 'getPreviewPath']),
            new TwigFunction('ws_preview_locales', [$this, 'getPreviewLocales']),
        ];
    }

    public function isEnabled(): bool
    {
        return $this->previewService->isEnabled();
    }

    public function getPreviewPath(object $entity, array $options = [], array $queryString = []): string
    {
        $domain = $this->context->getDomain();
        if (null === $domain) {
            throw new \RuntimeException();
        }

        $entityClassName = (new \ReflectionClass($entity))->getName();

        $extraQueryString = '';
        if (!empty($queryString)) {
            $extraQueryString = '&' . http_build_query($queryString);
        }

        return sprintf(
            'https://%s/%s?hash=%s%s',
            $domain->getHost(),
            $this->previewService->getPath(),
            $this->previewService->hash($entityClassName, $options),
            $extraQueryString
        );
    }

    public function getPreviewLocales(): array
    {
        return $this->previewService->getLocales();
    }
}
