<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Twig\Tag\PageConfiguration\PageConfigurationTokenParser;

class PageConfigurationExtension extends AbstractExtension
{
    protected string $title;
    protected string $header;
    protected string $subheader;
    protected array $breadcrumbs;

    public function __construct()
    {
        $this->title = 'CMS';
        $this->header = 'CMS';
        $this->subheader = '';
        $this->breadcrumbs = [];
    }

    public function getTokenParsers(): array
    {
        return [
            new PageConfigurationTokenParser(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_title', [$this, 'getTitle']),
            new TwigFunction('get_header', [$this, 'getHeader']),
            new TwigFunction('get_subheader', [$this, 'getSubheader']),
            new TwigFunction('get_breadcrumbs', [$this, 'getBreadcrumbs']),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getSubheader(): string
    {
        return $this->subheader;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function configure($config): void
    {
        if (isset($config['title'])) {
            if (empty($this->title)) {
                $this->title = $config['title'];
            }
        }

        if (isset($config['header'])) {
            if (empty($this->header)) {
                $this->header = $config['header'];
            }
        }

        if (isset($config['subheader'])) {
            if (empty($this->subheader)) {
                $this->subheader = $config['subheader'];
            }
        }

        if (isset($config['breadcrumbs']) && is_array($config['breadcrumbs'])) {
            if (empty($this->breadcrumbs)) {
                $this->breadcrumbs = $config['breadcrumbs'];
            }
        }
    }
}
