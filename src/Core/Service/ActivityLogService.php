<?php

namespace WS\Core\Service;

use WS\Core\Library\ActivityLog\ActivityLogInterface;

class ActivityLogService
{
    protected array $supportedEntities = [];

    public function __construct(protected bool $enabled)
    {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function registerService(ActivityLogInterface $service): void
    {
        foreach($service->getActivityLogSupported() as $supportedClass) {
            $this->supportedEntities[$supportedClass] = $service;
        }
    }

    public function getEntityId(string $className, object $entity): string
    {
        return $this->supportedEntities[$className]->getActivityLogEntityId($entity);
    }

    public function getClassName(string $className): string
    {
        return $this->supportedEntities[$className]->getActivityLogClassName($className);
    }

    public function isSupported(string $className, ?string $operation = null): bool
    {
        return
            isset($this->supportedEntities[$className]) &&
            $this->supportedEntities[$className]->isActivityLogSpported($className, $operation);
    }

    public function getService(string $className): ActivityLogInterface
    {
        return $this->supportedEntities[$className];
    }

    public function getServices(): array
    {
        return array_keys($this->supportedEntities);
    }
}
