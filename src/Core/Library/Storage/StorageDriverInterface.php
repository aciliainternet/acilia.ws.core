<?php

namespace WS\Core\Library\Storage;

interface StorageDriverInterface
{
    public const string CONTEXT_PUBLIC = 'public';
    public const string CONTEXT_URL = 'url';
    public const string CONTEXT_PRIVATE = 'private';

    public function getName(): string;

    public function setConfiguration(): void;

    public function getStorageMetadata(): array;

    public function save(string $filePath, string $content, string $context): void;

    public function get(string $filePath, string $context, array $options): string;

    public function exists(string $filePath, string $context, array $options): bool;

    public function getPublicUrl(string $filePath, array $options): string;

    public function sanitizeFilename(string $filename, string $extension): string;
}
