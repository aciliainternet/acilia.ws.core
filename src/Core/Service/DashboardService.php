<?php

namespace WS\Core\Service;

use WS\Core\Library\Dashboard\DashboardWidgetInterface;
use Twig\Environment;

class DashboardService
{
    protected Environment $twig;
    protected array $widgets = [];

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function addWidget(DashboardWidgetInterface $widget): void
    {
        $this->widgets[$widget->getId()] = $widget;
    }

    public function getWidget(string $id): DashboardWidgetInterface
    {
        if (!array_key_exists($id, $this->widgets)) {
            throw new \Exception(sprintf('There is no Widget registered with id "%s"', $id));
        }

        return $this->widgets[$id];
    }

    public function getWidgets(): array
    {
        $widgets = $this->widgets;

        usort($widgets, function (DashboardWidgetInterface $a, DashboardWidgetInterface $b) {
            return ($a->getOrder() < $b->getOrder()) ? -1 : (($a->getOrder() > $b->getOrder()) ? 1 : 0);
        });

        return $widgets;
    }

    public function render(string $id): string
    {
        try {
            $template = $this->getWidget($id)->getTemplate();
            $data = $this->getWidget($id)->getData();

            return $this->twig->render($template, $data);
        } catch (\Exception $e) {
        }

        return sprintf(' <!-- Dashboard widget with id "%s" cannot be loaded --> ', $id);
    }
}
