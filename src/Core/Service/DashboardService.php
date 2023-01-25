<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Twig\Environment;
use WS\Core\Library\Dashboard\DashboardWidgetInterface;

class DashboardService
{
    public function __construct(
        #[TaggedLocator(DashboardWidgetInterface::class, defaultIndexMethod: 'getId', defaultPriorityMethod: 'getPriority')]
        private ServiceLocator $widgets,
        private Environment $twig
    ) {
    }

    public function getWidgets(): array
    {
        return array_keys($this->widgets->getProvidedServices());
    }

    public function render(string $id): string
    {
        try {
            $template = $this->widgets->get($id)->getTemplate();
            $data = $this->widgets->get($id)->getData();

            return $this->twig->render($template, $data);
        } catch (\Exception) {
        }

        return sprintf(' <!-- Dashboard widget with id "%s" cannot be loaded --> ', $id);
    }
}
