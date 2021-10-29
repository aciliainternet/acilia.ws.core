<?php

namespace WS\Core\Library\Alert;

class GatherAlertsEvent
{
    protected array $alerts;

    public function addAlert(AlertMessage $alert): void
    {
        $this->alerts[] = $alert;
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }
}
