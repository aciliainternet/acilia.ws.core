<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use WS\Core\Library\Alert\AlertGathererInterface;
use WS\Core\Library\Alert\GatherAlertsEvent;

class AlertService
{
    protected ?array $alerts = null;

    public function __construct(
        #[TaggedLocator(AlertGathererInterface::class)]
        private ServiceLocator $gatherers
    ) {
    }

    public function getAlerts(): array
    {
        if ($this->alerts === null) {
            $event = new GatherAlertsEvent();

            foreach ($this->gatherers as $gatherer) {
                $gatherer->gatherAlerts($event);
            }

            $this->alerts = $event->getAlerts();
        }

        return $this->alerts;
    }
}
