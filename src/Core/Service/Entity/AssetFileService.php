<?php

namespace WS\Core\Service\Entity;

use WS\Core\Entity\AssetFile;
use WS\Core\Library\FactoryCollector\FactoryCollectorInterface;
use WS\Core\Repository\AssetFileRepository;
use WS\Core\Service\ContextService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AssetFileService implements FactoryCollectorInterface
{
    protected LoggerInterface $logger;
    protected EntityManagerInterface $em;
    protected AssetFileRepository $repository;
    protected ContextService $contextService;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        ContextService $contextService
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->repository = $this->em->getRepository(AssetFile::class);
        $this->contextService = $contextService;
    }

    public function getSortFields(): array
    {
        return ['createdAt', 'filename'];
    }

    public function createFromUploadedFile(UploadedFile $fileFile, $entity = null, string $fileField = null): AssetFile
    {
        $assetFile = (new AssetFile())
            ->setFilename($this->sanitizeFilename($fileFile))
            ->setMimeType((string) $fileFile->getMimeType())
            ->setStorageMetadata($storageMetadata);

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

    public function getFactoryCollectorSupported(): array
    {
        return [AssetFile::class];
    }

    public function getAvailableByIds(array $ids): array
    {
        $result = [];

        try {
            $data = $this->repository->getAvailableByIds($this->contextService->getDomain(), $ids);
            foreach ($data as $entity) {
                $result[$entity->getId()] = $entity;
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error fetching file assets. Error %s', $e->getMessage()));

            return [];
        }
    }

    protected function sanitizeFilename(UploadedFile $fileFile): string
    {
        $filename = explode('.', (string) $fileFile->getClientOriginalName());
        $assetName = (string) preg_replace('/[^\w\-\.]/', '', $filename[0]);
        $filename = sprintf('%s.%s', $assetName, $fileFile->getClientOriginalExtension());

        return trim($filename);
    }
}
