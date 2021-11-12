<?php

namespace WS\Core\Library\ActivityLog;

class ActivityLogChanges
{
    protected string $field;
    protected mixed $before;
    protected mixed $after;

    public function __construct(string $field, mixed $before, mixed $after)
    {
        $this->field = $field;
        $this->before = $before;
        $this->after = $after;
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
