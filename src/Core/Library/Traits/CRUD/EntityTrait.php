<?php

namespace WS\Core\Library\Traits\CRUD;

trait EntityTrait
{
    public function getEntityClass(): string
    {
        $className = get_class($this);
        $classPath = explode('\\', $className);

        if ($classPath[0] === 'WS') {
            $classname = str_replace(['Service', 'Repository'], '', $classPath[4]);
            return sprintf('WS\Core\Entity\%s', $classname);

        } elseif ($classPath[0] === 'App' && $classPath[1] === 'Service') {
            $classname = str_replace('Service', '', $classPath[3]);
            return sprintf('App\Entity\%s', $classname);
            
        } elseif ($classPath[0] === 'App' && $classPath[1] === 'Repository') {
            $classname = str_replace('Repository', '', $classPath[2]);
            return sprintf('App\Entity\%s', $classname);

        } else {
            throw new \Exception('Unable to find class Entity');
        }
    }

}
