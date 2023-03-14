<?php

namespace WS\Core\Library\Traits\CRUD;

/**
 * @method class-string<object> getEntityClass()
 */
trait EntityTrait
{
    public function getEntityClass(): string
    {
        $className = get_class($this);
        $classPath = explode('\\', $className);

        if ($classPath[0] === 'WS' && $classPath[2] === 'Service') {
            $shortClassName = str_replace('Service', '', $classPath[4]);
            return sprintf('WS\Core\Entity\%s', $shortClassName);

        } elseif ($classPath[0] === 'WS' && $classPath[2] === 'Repository') {
            $shortClassName = str_replace('Repository', '', $classPath[3]);
            return sprintf('WS\Core\Entity\%s', $shortClassName);

        } elseif ($classPath[0] === 'App' && $classPath[1] === 'Service') {
            $shortClassName = str_replace('Service', '', $classPath[3]);
            return sprintf('App\Entity\%s', $shortClassName);

        } elseif ($classPath[0] === 'App' && $classPath[1] === 'Repository') {
            $shortClassName = str_replace('Repository', '', $classPath[2]);
            return sprintf('App\Entity\%s', $shortClassName);

        } else {
            throw new \Exception('Unable to find class Entity');
        }
    }
}
