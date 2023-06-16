<?php

namespace WS\Core\Library\Storage;

interface StorageDriverInterface
{
    public const CONTEXT_PUBLIC = 'public';
    public const CONTEXT_URL = 'url';
    public const CONTEXT_PRIVATE = 'private';

    public function getName(): string;

    public function setConfiguration(): void;

    public function getStorageMetadata(): array;

    public function save(string $filePath, string $content, string $context): void;

    public function get(string $filePath, string $context, array $options): string;

    public function exists(string $filePath, string $context, array $options): bool;

    public function getPublicUrl(string $filePath, array $options): string;
}
