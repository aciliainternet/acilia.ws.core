<?php

namespace WS\Core\Library\Attribute\CRUD;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class SortField
{
    public function __construct(public string $dir = 'ASC', public ?string $name = null, public ?int $order = null)
    {
    }
}
