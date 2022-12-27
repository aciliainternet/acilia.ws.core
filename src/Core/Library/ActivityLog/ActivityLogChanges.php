<?php

namespace WS\Core\Library\ActivityLog;

class ActivityLogChanges
{
    public function __construct(
        protected string $field,
        protected mixed $before,
        protected mixed $after
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getBefore(): mixed
    {
        return $this->before;
    }

    public function getAfter(): mixed
    {
        return $this->after;
    }
}
