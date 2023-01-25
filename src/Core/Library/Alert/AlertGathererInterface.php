<?php

namespace WS\Core\Library\Alert;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface AlertGathererInterface
{
    public function gatherAlerts(GatherAlertsEvent $event): void;
}
