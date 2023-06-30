<?php

namespace WS\Core\Library\ActivityLog;

use WS\Core\Library\CRUD\AbstractService;

trait ActivityLogTrait
{
    public function getActivityLogEntityId(object $entity): int
    {
        return $entity->getId();
    }

    public function getActivityLogClassName(string $className): string
    {
        return $className;
    }

    public function isActivityLogSpported(string $className, ?string $operation): bool
    {
        return true;
    }

    public function getActivityLogSupported(): array
    {
        if ($this instanceof AbstractService) {
            return [$this->getEntityClass()];
        }

        throw new \Exception(sprintf(
            'Your service "%s" must implement the method "getActivityLogSupported" or imlements the abstract service "WS\Core\Library\CRUD\AbstractService()"',
            static::class
        ));
    }

    public function getActivityLogFields(): ?array
    {
        return null;
    }
}
