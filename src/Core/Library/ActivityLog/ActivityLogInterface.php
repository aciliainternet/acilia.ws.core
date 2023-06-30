<?php

namespace WS\Core\Library\ActivityLog;

interface ActivityLogInterface
{
    public const UPDATE = 'update';
    public const CREATE = 'create';
    public const DELETE = 'delete';

    public function getActivityLogEntityId(object $entity): int;

    public function getActivityLogClassName(string $class): string;

    public function isActivityLogSpported(string $className, ?string $operation): bool;

    public function getActivityLogSupported(): array;

    public function getActivityLogFields(): ?array;
}
