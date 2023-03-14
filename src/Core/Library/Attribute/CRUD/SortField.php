<?php

namespace WS\Core\Library\Attribute\CRUD;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SortField
{
    public function __construct(public string $dir = 'ASC')
    {
    }
}
