<?php

namespace WS\Core\Library\Storage;

class LocalDriver implements StorageDriverInterface
{
    public function save(string $resource, string $context): void
    {
    }

    public function get(string $resource, string $context): string
    {
        return '';
    }
}
