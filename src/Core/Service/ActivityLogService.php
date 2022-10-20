<?php

namespace WS\Core\Service;

use WS\Core\Library\ActivityLog\ActivityLogInterface;

class ActivityLogService
{
    protected bool $enabled;
    protected array $supportedEntities = [];

    public function __construct(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function registerService(ActivityLogInterface $service): void
    {
        $this->supportedEntities[$service->getActivityLogSupported()] = $service;
    }

    public function isSupported(string $className): bool
    {
        return isset($this->supportedEntities[$className]);
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
