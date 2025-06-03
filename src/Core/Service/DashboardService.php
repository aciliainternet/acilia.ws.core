<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Twig\Environment;
use WS\Core\Library\Dashboard\DashboardWidgetInterface;

class DashboardService
{
    public function __construct(
        #[AutowireLocator(DashboardWidgetInterface::class, defaultIndexMethod: 'getId', defaultPriorityMethod: 'getPriority')]
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
            /** @var DashboardWidgetInterface $dashboardWidgeService */
            $dashboardWidgeService = $this->widgets->get($id);

            $template = $dashboardWidgeService->getTemplate();
            $data = $dashboardWidgeService->getData();

            return $this->twig->render($template, $data);
        } catch (\Exception) {
        }

        return sprintf(' <!-- Dashboard widget with id "%s" cannot be loaded --> ', $id);
    }
}
