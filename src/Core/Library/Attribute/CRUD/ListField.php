<?php

namespace WS\Core\Library\Attribute\CRUD;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class ListField
{
    public function __construct(
        public ?int $order = null,
        public ?string $filter = null,
        public array $options = [],
        public bool $isDate =  false
    ) {
    }
}
