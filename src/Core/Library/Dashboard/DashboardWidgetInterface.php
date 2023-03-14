<?php

namespace WS\Core\Library\Dashboard;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface DashboardWidgetInterface
{
    public static function getId(): string;

    public static function getPriority(): int;

    public function getTemplate(): string;

    public function getData(): array;
}
