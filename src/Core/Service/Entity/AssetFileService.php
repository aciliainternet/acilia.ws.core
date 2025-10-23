<?php

namespace WS\Core\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WS\Core\Entity\AssetFile;
use WS\Core\Library\FactoryCollector\FactoryCollectorInterface;
use WS\Core\Repository\AssetFileRepository;
use WS\Core\Service\ContextInterface;

class AssetFileService implements FactoryCollectorInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected EntityManagerInterface $em,
        protected ContextInterface $context,
        protected AssetFileRepository $repository
    ) {
    }

    public function getSortFields(): array
    {
        return ['createdAt', 'filename'];
    }

    public function getFilterFields(): array
    {
        return ['filename'];
    }

    public function createFromUploadedFile(
        UploadedFile $fileFile,
        string $sanitizedFilename,
        ?object $entity = null,
        ?string $fileField = null,
        array $storageMetadata = []
    ): AssetFile {
        $storageMetadata['original_filename'] = $fileFile->getClientOriginalName();

        $assetFile = (new AssetFile())
            ->setFilename($sanitizedFilename)
            ->setMimeType((string) $fileFile->getMimeType())
            ->setStorageMetadata($storageMetadata);

        if (null !== $entity && null !== $fileField) {
            $fieldSetter = sprintf('set%s', ucfirst((string) $fileField));
            if (method_exists($entity, $fieldSetter)) {
                try {
                    // set asset file into entity
                    $ref = new \ReflectionMethod(get_class($entity), $fieldSetter);
                    $ref->invoke($entity, $assetFile);
                } catch (\ReflectionException $e) {
                    $this->logger->error(sprintf('Error setting AssetFile into Entity. Error: %s', $e->getMessage()));
                }
            }
        }

        // save asset file
        $this->em->persist($assetFile);
        $this->em->flush();

        return $assetFile;
    }

    public function clone(AssetFile $originalAssetFile): AssetFile
    {
        $assetFile = clone $originalAssetFile;

        $this->em->persist($assetFile);
        $this->em->flush();

        return $assetFile;
    }

    #[\Override]
    public function getFactoryCollectorSupported(): array
    {
        return [AssetFile::class];
    }

    #[\Override]
    public function getAvailableByIds(array $ids): array
    {
        $result = [];

        try {
            $data = $this->repository->getAvailableByIds($this->context->getDomain(), $ids);
            foreach ($data as $entity) {
                $result[$entity->getId()] = $entity;
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error fetching file assets. Error %s', $e->getMessage()));

            return [];
        }
    }
}
