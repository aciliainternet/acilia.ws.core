<?php

namespace WS\Core\Library\Attribute\CRUD;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SortField
{
    public string $order;

    public function getOrder(): string
    {
        return $this->order;
    }
}
