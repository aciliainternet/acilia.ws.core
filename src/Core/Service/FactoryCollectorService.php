<?php

namespace WS\Core\Service;

use WS\Core\Library\FactoryCollector\FactoryCollector;
use WS\Core\Library\FactoryCollector\FactoryCollectorInterface;

class FactoryCollectorService
{
    protected array $services = [];
    protected array $supportedEntities = [];
    protected array $collect = [];
    protected array $objects = [];

    public function registerService(FactoryCollectorInterface $service): void
    {
        $this->services[] = $service;

        foreach ($service->getFactoryCollectorSupported() as $entityName) {
            $this->supportedEntities[] = $entityName;
        }
    }

    public function isSupported(string $className): bool
    {
        return in_array($className, $this->supportedEntities);
    }

    public function getCollector(): FactoryCollector
    {
        return new FactoryCollector($this);
    }

    public function fetch(array $collection): array
    {
        $objects = [];
        foreach ($collection as $className => $data) {
            $toCollect = [];
            foreach ($data as $objectId) {
                if (!isset($this->objects[$className][$objectId])) {
                    $toCollect[] = $objectId;
                } else {
                    $objects[$className][$objectId] = $this->objects[$className][$objectId];
                }
            }

            foreach ($this->services as $service) {
                if (in_array($className, $service->getFactoryCollectorSupported())) {
                    $collectedObjects = $service->getAvailableByIds($toCollect);
                    foreach ($collectedObjects as $objectId => $object) {
                        $this->objects[$className][$objectId] = $object;
                        $objects[$className][$objectId] = $object;
                    }

                    break;
                }
            }
        }

        return $objects;
    }
}
