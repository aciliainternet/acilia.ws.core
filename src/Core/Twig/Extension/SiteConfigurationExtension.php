<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Twig\Tag\SiteConfiguration\SiteConfigurationTokenParser;

class SiteConfigurationExtension extends AbstractExtension
{
    private array $urls = [];

    public function getTokenParsers(): array
    {
        return [
            new SiteConfigurationTokenParser(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_url', [$this, 'hasUrl']),
            new TwigFunction('get_url', [$this, 'getUrl']),
        ];
    }

    public function hasUrl(): bool
    {
        return count($this->urls) > 0;
    }

    public function getUrl(?string $language = null): ?string
    {
        if (null !== $language) {
            return $this->urls[$language] ?? null;
        }

        return !empty($this->urls) ? array_shift($this->urls) : null;
    }

    public function configure(array $config): void
    {
        $this->urls = $config['urls'] ?? [];
    }
}
