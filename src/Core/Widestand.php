<?php

namespace WS\Core;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ws.component')]
class Widestand
{
    public const string NAME = 'Core';
    public const string VERSION = '1.2025-06-01';
}
