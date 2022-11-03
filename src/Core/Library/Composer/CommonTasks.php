<?php

namespace WS\Core\Library\Composer;

abstract class CommonTasks
{
    abstract public static function getAssetsSource(): string;

    abstract public static function getAssetsTarget(): string;

    public static function linkAssets(): void
    {
        $target = '../../' . str_replace(getcwd() . '/', '', static::getAssetsSource());
        $link = static::getAssetsTarget();

        if (file_exists($link)) {
            if (!is_link($link)) {
                throw new \Exception(sprintf('Error, "%s" is not a symlink.', $link));
            }

            if (readlink($link) !== $target) {
                throw new \Exception(sprintf('Error, symlink "%s" does not point to "%s".', $link, $target));
            }
        } else {
            if (!file_exists(dirname($link))) {
                mkdir(dirname($link));
            }
            var_dump($target, $link);
            symlink($target, $link);
        }
    }
}
