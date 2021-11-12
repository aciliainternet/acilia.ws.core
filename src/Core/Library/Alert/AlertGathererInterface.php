<?php

namespace WS\Core\Library\Alert;

interface AlertGathererInterface
{
    public function gatherAlerts(GatherAlertsEvent $event): void;
}
