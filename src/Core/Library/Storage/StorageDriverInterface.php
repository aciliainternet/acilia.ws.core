<?php

namespace WS\Core\Library\Storage;

interface StorageDriverInterface
{
    const CONTEXT_PUBLIC = 'public';
    const CONTEXT_URL = 'url';
    const CONTEXT_PRIVATE = 'private';

    public function getName(): string;

    public function setConfiguration(): void;

    public function getStorageMetadata(): array;

    public function save(string $filePath, string $content, string $context): void;

    public function get(string $filePath, string $context): string;

    public function exists(string $filePath, string $context): bool;

    public function getPublicUrl(string $filePath): string;

    public function getPrivateUrl(string $filePath): string;

    public function getPublicPath(string $filePath): string;
}
