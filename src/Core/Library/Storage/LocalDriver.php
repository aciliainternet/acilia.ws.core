<?php

namespace WS\Core\Library\Storage;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LocalDriver implements StorageDriverInterface
{
    protected array $storageContext;

    public function __construct(private ParameterBagInterface $params)
    {
    }

    public function getName(): string
    {
        return 'local';
    }

    public function setConfiguration(): void
    {
        $contextPrivate = sprintf('%s/storage', $this->params->get('kernel.project_dir'));
        $contextPublic = sprintf('%s/public/storage', $this->params->get('kernel.project_dir'));
        $contextUrl = '/storage';
        if ($this->params->has('storage.configuration')) {
            if (isset($this->params->get('storage.configuration')['context.private'])) {
                $contextPrivate = $this->params->get('storage.configuration')['context.private'];
            }
            if (isset($this->params->get('storage.configuration')['context.public'])) {
                $contextPublic = $this->params->get('storage.configuration')['context.public'];
            }
            if (isset($this->params->get('storage.configuration')['context.url'])) {
                $contextUrl = $this->params->get('storage.configuration')['context.url'];
            }
        }

        $this->storageContext = [
            StorageDriverInterface::CONTEXT_PRIVATE => $contextPrivate,
            StorageDriverInterface::CONTEXT_PUBLIC => $contextPublic,
            StorageDriverInterface::CONTEXT_URL => $contextUrl,
        ];
    }

    public function getStorageMetadata(): array
    {
        return [];
    }

    public function save(string $filePath, string $content, string $context): void
    {
        $finalFile = sprintf('%s/%s', $this->storageContext[$context], $filePath);

        if (!is_dir(dirname($finalFile))) {
            mkdir(dirname($finalFile), 0766, true);
        }

        file_put_contents($finalFile, $content);
    }

    public function get(string $filePath, string $context, array $options): string
    {
        $finalFile = sprintf('%s/%s', $this->storageContext[$context], $filePath);
        if (!file_exists($finalFile) || !is_readable($finalFile)) {
            throw new \Exception(sprintf('File "%s" does not exists or is not readable.', $finalFile));
        }

        $finalFileContent = file_get_contents($finalFile);
        if (false === $finalFileContent) {
            throw new \Exception(sprintf('File "%s" exists but cannot be opened.', $finalFile));
        }

        return $finalFileContent;
    }

    public function exists(string $filePath, string $context, array $options): bool
    {
        return \file_exists(sprintf('%s/%s', $this->storageContext[$context], $filePath));
    }

    public function getPublicUrl(string $filePath, array $options): string
    {
        return sprintf('%s/%s', $this->storageContext[self::CONTEXT_URL], $filePath);
    }
}
