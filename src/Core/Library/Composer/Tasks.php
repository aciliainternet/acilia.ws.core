<?php

namespace WS\Core\Library\Composer;

class Tasks extends CommonTasks
{
    public static function getAssetsSource(): string
    {
        return (string) realpath(__DIR__ . '/../Resources/assets/dist');
    }

    public static function getAssetsTarget(): string
    {
        return 'public/cms';
    }
}
