<?php

namespace WS\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFilter;
use WS\Core\Library\Publishing\PublishingEntityInterface;

class PublishingExtension
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    #[AsTwigFilter(name: PublishingEntityInterface::FILTER_STATUS, isSafe: ['html'])]
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
