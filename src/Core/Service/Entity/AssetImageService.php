<?php

namespace WS\Core\Service\Entity;

use WS\Core\Entity\AssetImage;
use WS\Core\Library\FactoryCollector\FactoryCollectorInterface;
use WS\Core\Repository\AssetImageRepository;
use WS\Core\Service\ContextService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AssetImageService implements FactoryCollectorInterface
{
    protected $logger;
    protected $em;

    /** @var AssetImageRepository */
    protected $repository;

    protected $contextService;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        ContextService $contextService
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->repository = $this->em->getRepository(AssetImage::class);
        $this->contextService = $contextService;
    }

    public function getSortFields(): array
    {
        return ['createdAt', 'filename'];
    }

    /**
     * @param string|null $search
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $dir
     *
     * @return array
     * @throws \Exception
     */
    public function getAll(?string $search, int $page, int $limit, string $sort = '', string $dir = ''): array
    {
        $offset = ($page - 1) * $limit;

        if ($sort) {
            if (!in_array($sort, $this->getSortFields())) {
                throw new \Exception('Sort by this field is not allowed');
            }
            $orderBy = [(string) $sort => $dir ? strtoupper($dir) : 'ASC'];
        } else {
            $orderBy = ['id' => 'DESC'];
        }

        // list only visible asset images
        $filter = ['visible' => true];

        try {
            return $this->repository->getAll($this->contextService->getDomain(), $search, $filter, $orderBy, $limit, $offset);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error fetching image assets. Error %s', $e->getMessage()));
        }

        return [];
    }

    public function createFromUploadedFile(UploadedFile $imageFile, $entity = null, string $imageField = null): AssetImage
    {
        $assetImage = new AssetImage();
        $assetImage
            ->setFilename($this->sanitizeFilename($imageFile))
            ->setMimeType((string) $imageFile->getMimeType())
        ;

        $assetImageInfo = getimagesize($imageFile->getPathname());
        if (false !== $assetImageInfo) {
            $assetImage
                ->setWidth($assetImageInfo[0])
                ->setHeight($assetImageInfo[1]);
        }

        if (null !== $entity) {
            $fieldSetter = sprintf('set%s', ucfirst((string) $imageField));
            if (method_exists($entity, $fieldSetter)) {
                try {
                    // set asset image into entity
                    $ref = new \ReflectionMethod(get_class($entity), $fieldSetter);
                    $ref->invoke($entity, $assetImage);
                } catch (\ReflectionException $e) {
                    $this->logger->error(sprintf('Error setting AssetImage into Entity. Error: %s', $e->getMessage()));
                }
            }
        }

        // Save Asset Image
        $this->em->persist($assetImage);
        $this->em->flush();

        return $assetImage;
    }

    public function createFromAsset($entity, $imageField, AssetImage $sourceAsset): AssetImage
    {
        $assetImage = new AssetImage();
        $assetImage
            ->setFilename($sourceAsset->getFilename())
            ->setMimeType($sourceAsset->getMimeType())
            ->setWidth($sourceAsset->getWidth())
            ->setHeight($sourceAsset->getHeight())
        ;

        try {
            // set asset image into entity
            $ref = new \ReflectionMethod(get_class($entity), sprintf('set%s', ucfirst($imageField)));
            $ref->invoke($entity, $assetImage);
        } catch (\ReflectionException $e) {
            $this->logger->error(sprintf('Error setting AssetImage into Entity. Error: %s', $e->getMessage()));
        }

        // save asset image
        $this->em->persist($assetImage);
        $this->em->flush();

        return $assetImage;
    }

    /**
     * @throws \Exception
     */
    public function create(AssetImage $image): AssetImage
    {
        try {
            $this->em->persist($image);
            $this->em->flush();

            $this->logger->info(sprintf('Created AssetImage ID::%s', $image->getId()));

            return $image;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error creating AssetImage. Error: %s', $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function edit(AssetImage $image): AssetImage
    {
        try {
            $this->em->flush();

            $this->logger->info(sprintf('Edited AssetImage ID::%s', $image->getId()));

            return $image;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error editing AssetImage ID::%s. Error: %s', $image->getId(), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param int $id
     *
     * @return AssetImage|null
     */
    public function get(int $id): ?AssetImage
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    /**
     * @throws \Exception
     */
    public function delete(AssetImage $image): void
    {
        $id = $image->getId();
        try {
            $this->em->remove($image);
            $this->em->flush();

            $this->logger->info(sprintf('Removed AssetImage ID::%s', $id));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error removing AssetImage ID::%s. Error: %s', $id, $e->getMessage()));

            throw $e;
        }
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
            $this->logger->error(sprintf('Error fetching image assets. Error %s', $e->getMessage()));

            return [];
        }
    }

    public function getFactoryCollectorSupported(): array
    {
        return [AssetImage::class];
    }

    protected function sanitizeFilename(UploadedFile $imageFile): string
    {
        $filename = explode('.', (string) $imageFile->getClientOriginalName());
        $imageName = (string) preg_replace('/[^\w\-\.]/', '', $filename[0]);
        $filename = sprintf('%s.%s', $imageName, $imageFile->getClientOriginalExtension());

        return trim($filename);
    }
}
