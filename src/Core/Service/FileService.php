<?php

namespace WS\Core\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WS\Core\Entity\AssetFile;
use WS\Core\Service\Entity\AssetFileService;

class FileService
{
    public function __construct(
        protected LoggerInterface $logger,
        protected AssetFileService $assetFileService,
        protected StorageService $storageService
    ) {
    }

    public function handle(UploadedFile $fileFile, object $entity, string $fileField, ?array $options = null): AssetFile
    {
        $fileFileContent = file_get_contents($fileFile->getPathname());
        if (false === $fileFileContent) {
            throw new \RuntimeException('File cannot be read');
        }

        $assetFile = $this->assetFileService->createFromUploadedFile($fileFile, $entity, $fileField);

        $this->storageService->save(
            $this->getFilePath($assetFile),
            $fileFileContent,
            $options['context'] ?? StorageService::CONTEXT_PRIVATE
        );

        return $assetFile;
    }

    public function handleStandalone(UploadedFile $fileFile, ?array $options = null): AssetFile
    {
        $fileFileContent = file_get_contents($fileFile->getPathname());
        if (false === $fileFileContent) {
            throw new \RuntimeException('File cannot be read');
        }

        $assetFile = $this->assetFileService->createFromUploadedFile($fileFile);

        $this->storageService->save(
            $this->getFilePath($assetFile),
            $fileFileContent,
            $options['context'] ?? StorageService::CONTEXT_PRIVATE
        );

        return $assetFile;
    }

    public function delete(object $entity, string $fileField): void
    {
        $fieldSetter = sprintf('set%s', ucfirst((string) $fileField));
        if (method_exists($entity, $fieldSetter)) {
            try {
                $ref = new \ReflectionMethod(\strval(\get_class($entity)), $fieldSetter);
                $ref->invoke($entity, null);
            } catch (\ReflectionException $e) {
                $this->logger->error(sprintf('Error deleting AssetFile on Entity. Error: %s', $e->getMessage()));
            }
        }
    }

    public function copy(AssetFile $originalAssetFile, ?array $options = null): ?AssetFile
    {
        if (!$this->storageService->exists(
            $this->getFilePath($originalAssetFile),
            $options['context'] ?? StorageService::CONTEXT_PRIVATE
        )) {
            $this->logger->error(sprintf(
                'Error copying AssetFile. File "%s" not found.',
                $this->getFilePath($originalAssetFile)
            ));

            return null;
        }

        $originalFileContent = file_get_contents(
            $this->storageService->getPrivateUrl($this->getFilePath($originalAssetFile))
        );
        if (false === $originalFileContent) {
            throw new \RuntimeException('File cannot be read');
        }

        $assetFile = $this->assetFileService->clone($originalAssetFile);

        $this->storageService->save(
            $this->getFilePath($assetFile),
            $originalFileContent,
            $options['context'] ?? StorageService::CONTEXT_PRIVATE
        );

        return $assetFile;
    }

    public function getFileUrl(AssetFile $assetFile): string
    {
        return $this->storageService->getPublicUrl($this->getFilePath($assetFile));
    }

    public function getFilePrivate(AssetFile $assetFile): string
    {
        return $this->storageService->getPrivateUrl($this->getFilePath($assetFile));
    }

    protected function getFilePath(AssetFile $assetFile): string
    {
        return sprintf(
            'files/%d/%d/%s',
            floor($assetFile->getId() / 1000),
            $assetFile->getId(),
            $assetFile->getFilename()
        );
    }
}
