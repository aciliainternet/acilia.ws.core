<?php

namespace WS\Core\Library\Alert;

class GatherAlertsEvent
{
    protected $alerts = [];

    public function addAlert(AlertMessage $alert)
    {
        $this->alerts[] = $alert;
    }

    public function getAlerts()
    {
        return $this->alerts;
    }
}
