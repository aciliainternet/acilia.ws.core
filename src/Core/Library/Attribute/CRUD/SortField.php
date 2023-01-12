<?php

namespace WS\Core\Library\Attribute\CRUD;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class SortField
{
    public string $dir;

    public function getDir(): string
    {
        return $this->dir;
    }
}
