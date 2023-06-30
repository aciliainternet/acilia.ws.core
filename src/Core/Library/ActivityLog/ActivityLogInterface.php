<?php

namespace WS\Core\Library\ActivityLog;

interface ActivityLogInterface
{
    public const UPDATE = 'update';
    public const CREATE = 'create';
    public const DELETE = 'delete';

    public function getActivityLogClassName(string $class): string;

    public function getActivityLogSupported(): array;

    public function getActivityLogFields(): ?array;
}
