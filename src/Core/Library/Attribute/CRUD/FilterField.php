<?php

namespace WS\Core\Library\Attribute\CRUD;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class FilterField
{
    public function __construct(public ?string $name = null)
    {
    }
}
