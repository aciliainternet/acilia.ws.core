<?php

namespace WS\Core\Library\Navigation;

class ResolvedPath
{
    protected string $name;
    protected array $attributes = [];

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addAttribute(string $name, mixed $value = null): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
