<?php

namespace WS\Core\Library\FactoryCollector;

use WS\Core\Service\FactoryCollectorService;

class FactoryCollector
{
    protected FactoryCollectorService $factoryService;
    protected array $collect;

    public function __construct(FactoryCollectorService $factoryService)
    {
        $this->factoryService = $factoryService;
        $this->collect = [];
    }

    public function add(string $className, array $data): void
    {
        if (! $this->factoryService->isSupported($className)) {
            throw new \Exception(sprintf('Service in Factory Service for class "%s" was not registered', $className));
        }

        foreach ($data as $objectId) {
            $this->collect[$className][$objectId] = $objectId;
        }
    }

    public function fetch(): array
    {
        return $this->factoryService->fetch($this->collect);
    }
}
