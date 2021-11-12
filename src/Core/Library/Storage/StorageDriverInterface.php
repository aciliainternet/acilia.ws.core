<?php

namespace WS\Core\Library\Storage;

interface StorageDriverInterface
{
    public function save(string $resource, string $context): void;

    public function get(string $resource, string $context): string;
}
