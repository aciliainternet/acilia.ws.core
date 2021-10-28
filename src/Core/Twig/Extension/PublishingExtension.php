<?php

namespace WS\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;
use WS\Core\Library\Publishing\PublishingEntityInterface;

class PublishingExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(PublishingEntityInterface::FILTER_STATUS, [$this, 'getStatus'], ['is_safe' => ['html']])
        ];
    }

    public function getStatus(?string $status, array $options): string
    {
        if ($status) {
            $statusText = $this->translator->trans(sprintf('publishing.publishStatus.%s.label', $status), [], 'ws_cms');

            if (isset($options['badge'])) {
                return sprintf('<span class="c-badge c-badge--%s c-badge--xsmall">%s</span>', $status, $statusText);
            }

            return $statusText;
        }

        return '-';
    }
}
