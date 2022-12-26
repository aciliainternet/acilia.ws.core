<?php

namespace WS\Core\Library\CRUD;

trait RouteTrait
{
    protected function getRouteNamePrefix(): string
    {
        $controllerClass = get_class($this);
        $classPath = explode('\\', $controllerClass);

        if ($classPath[0] === 'WS') {
            $controllerName = strtolower(str_replace('Controller', '', $classPath[4]));
            $prefix = sprintf('ws_cms_%s_%s', $classPath[1], $controllerName);
        } elseif ($classPath[0] === 'App') {
            $controllerName = str_replace('Controller', '', $classPath[count($classPath) - 1]);
            $controllerWords = preg_split('/(?=[A-Z])/', $controllerName, -1, PREG_SPLIT_NO_EMPTY);
            if (false === $controllerWords) {
                throw new \Exception(sprintf('Unable to calculate Route Name Prefix for Controller: %s', $controllerClass));
            }
            $controllerPrefix = implode('_', $controllerWords);
            $prefix = sprintf('cms_%s', $controllerPrefix);
        } else {
            throw new \Exception(sprintf('Unable to calculate Route Name Prefix for Controller: %s', $controllerClass));
        }

        return strtolower($prefix);
    }
}
