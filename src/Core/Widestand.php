<?php

namespace WS\Core;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ws.component')]
class Widestand
{
    public const NAME = 'Core';
    public const VERSION = '1.2023-01-01';
}
