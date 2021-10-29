<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WS\Core\Library\Storage\StorageDriverInterface;

class StorageService
{
    public const CONTEXT_PUBLIC = 'public';
    public const CONTEXT_URL = 'url';
    public const CONTEXT_PRIVATE = 'private';

    protected StorageDriverInterface $driver;
    protected array $storage;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->storage = [
            self::CONTEXT_PRIVATE => sprintf('%s/storage', \strval($parameterBag->get('kernel.project_dir'))),
            self::CONTEXT_PUBLIC => sprintf('%s/public/storage', \strval($parameterBag->get('kernel.project_dir'))),
            self::CONTEXT_URL => '/storage',
        ];
    }

    public function save(string $filePath, string $content, string $context): self
    {
        //$this->driver->save($resource, $context);

        $finalFile = sprintf('%s/%s', $this->storage[$context], $filePath);

        if (!is_dir(dirname($finalFile))) {
            mkdir(dirname($finalFile), 0766, true);
        }

        file_put_contents($finalFile, $content);

        return $this;
    }

    public function get(string $filePath, string $context): string
    {
        //return $this->driver->get($resource, $context);

        $finalFile = sprintf('%s/%s', $this->storage[$context], $filePath);
        if (!file_exists($finalFile) || !is_readable($finalFile)) {
            throw new \Exception(sprintf('File "%s" does not exists or is not readable.', $finalFile));
        }

        $finalFileContent = file_get_contents($finalFile);
        if (false === $finalFileContent) {
            throw new \Exception(sprintf('File "%s" exists but cannot be opened.', $finalFile));
        }

        return $finalFileContent;
    }

    public function exists(string $filePath, string $context): bool
    {
        return \file_exists(sprintf('%s/%s', $this->storage[$context], $filePath));
    }

    public function getPublicUrl(string $filePath): string
    {
        return sprintf('%s/%s', $this->storage[self::CONTEXT_URL], $filePath);
    }

    public function getPrivateUrl(string $filePath): string
    {
        return sprintf('%s/%s', $this->storage[self::CONTEXT_PRIVATE], $filePath);
    }

    public function getPublicPath(string $filePath): string
    {
        return sprintf('%s/%s', $this->storage[self::CONTEXT_PUBLIC], $filePath);
    }
}
