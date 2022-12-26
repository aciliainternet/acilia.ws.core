<?php

namespace WS\Core\Library\ActivityLog;

interface ActivityLogInterface
{
    public const UPDATE = 'update';
    public const CREATE = 'create';
    public const DELETE = 'delete';

    public function getActivityLogSupported(): string;

    public function getActivityLogFields(): ?array;
}
