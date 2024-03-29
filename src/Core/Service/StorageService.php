<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WS\Core\Library\Storage\StorageDriverException;
use WS\Core\Library\Storage\StorageDriverInterface;

class StorageService
{
    protected string $driverName;
    protected array $drivers;

    public function __construct(protected ParameterBagInterface $parameterBag)
    {
        $this->driverName = $parameterBag->get('storage.driver');
    }

    public function getDriver(string $driverName): StorageDriverInterface
    {
        if (isset($this->drivers[$driverName])) {
            return $this->drivers[$driverName];
        }

        throw new StorageDriverException(
            sprintf('Invalid driver storage "%s"', $driverName)
        );
    }

    public function addDriver(StorageDriverInterface $driver): void
    {
        $driver->setConfiguration();
        $this->drivers[$driver->getName()] = $driver;
    }

    public function getStorageMetadata(): array
    {
        return $this->getDriver($this->driverName)->getStorageMetadata();
    }

    public function save(string $filePath, string $content, string $context): void
    {
        $this->getDriver($this->driverName)->save($filePath, $content, $context);
    }

    public function get(string $filePath, string $context, array $options = []): string
    {
        return $this->getDriver($this->driverName)->get($filePath, $context, $options);
    }

    public function exists(string $filePath, string $context, array $options = []): bool
    {
        return $this->getDriver($this->driverName)->exists($filePath, $context, $options);
    }

    public function getPublicUrl(string $filePath, array $options = []): string
    {
        return $this->getDriver($this->driverName)->getPublicUrl($filePath, $options);
    }
}
