<?php

namespace WS\Core\Library\Sidebar;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface SidebarDefinitionInterface
{
    public static function getPriority(): int;
    
    public function getSidebarDefinition(): array;

    public function getSidebarAssets(): array;
}
