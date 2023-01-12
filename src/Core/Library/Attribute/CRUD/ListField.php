<?php

namespace WS\Core\Library\Attribute\CRUD;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ListField
{
    public int $order;
    public string $fiter;
    public array $options;
    public int $width;
    public bool $isDate;

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getFiter() : string
    {
        return $this->fiter;
    }

    public function getOptions() : array
    {
        return $this->options;
    }

    public function getWidth() : int
    {
        return $this->width;
    }

    public function isIsDate() : bool
    {
        return $this->isDate;
    }
}