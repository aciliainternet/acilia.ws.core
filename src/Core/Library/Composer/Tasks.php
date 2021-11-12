<?php

namespace WS\Core\Library\Composer;

class Tasks extends CommonTasks
{
    public static function getAssetsSource(): string
    {
        return (string) realpath(__DIR__ . '/../../Resources/assets');
    }

    public static function getAssetsTarget(): string
    {
        return 'assets/ws/core';
    }
}
