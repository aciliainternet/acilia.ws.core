<?php

namespace WS\Core\Library\FactoryCollector;

use WS\Core\Library\CRUD\AbstractService;

trait FactoryCollectorTrait
{
    public function getAvailableByIds(array $ids): array
    {
        return [];
    }

    public function getFactoryCollectorSupported(): array
    {
        if ($this instanceof AbstractService) {
            return [$this->getEntityClass()];
        }

        throw new \Exception(sprintf(
            'Your service "%s" must implement the method "getFactoryCollectorSupported" or imlements the abstract service "WS\Core\Library\CRUD\AbstractService()"',
            static::class
        ));
    }
}
