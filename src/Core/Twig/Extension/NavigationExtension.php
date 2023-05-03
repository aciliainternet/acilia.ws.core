<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Entity\Navigation;
use WS\Core\Service\Entity\NavigationService;

class NavigationExtension extends AbstractExtension
{
    public function __construct(private NavigationService $service)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cms_get_navigation_tree', [$this, 'getNavigationTree']),
        ];
    }

    public function getNavigationTree(Navigation $navigation): array
    {
        return $this->service->getNavigationTree($navigation);
    }
}
