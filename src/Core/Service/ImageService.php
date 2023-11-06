<?php

namespace WS\Core\Service;

use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WS\Core\Entity\AssetImage;
use WS\Core\Library\Asset\ImageRenditionInterface;
use WS\Core\Library\Asset\RenditionDefinition;
use WS\Core\Library\Storage\StorageDriverInterface;
use WS\Core\Service\Entity\AssetImageService;

class ImageService
{
    protected ImageManager $imageManager;
    protected array $renditions;
    protected array $renderMethods = [];

    public function __construct(
        protected LoggerInterface $logger,
        protected AssetImageService $assetImageService,
        protected StorageService $storageService
    ) {
        $this->imageManager = new ImageManager(['driver' => 'imagick']);

        $this->registerRenderMethod(RenditionDefinition::METHOD_CROP, \Closure::fromCallable([$this, 'renderMethodCrop']));
        $this->registerRenderMethod(RenditionDefinition::METHOD_THUMB, \Closure::fromCallable([$this, 'renderMethodThumb']));
    }

    public function registerRenditions(ImageRenditionInterface $service): void
    {
        foreach ($service->getRenditionDefinitions() as $definition) {
            if ($definition instanceof RenditionDefinition) {
                $this->addRendition($definition);
            }
        }
    }

    public function addRendition(RenditionDefinition $definition): void
    {
        if (!isset($this->renditions[$definition->getClass()])) {
            $this->renditions[$definition->getClass()] = [];
        }

        if (!isset($this->renditions[$definition->getClass()][$definition->getField()])) {
            $this->renditions[$definition->getClass()][$definition->getField()] = [];
        }

        $this->renditions[$definition->getClass()][$definition->getField()][$definition->getName()] = $definition;
    }

    public function getRenditions(string $class, string $field): array
    {
        if (isset($this->renditions[$class]) && isset($this->renditions[$class][$field])) {
            return $this->renditions[$class][$field];
        }

        return [];
    }

    public function getAspectRatios(string $class, string $field): array
    {
        $aspectRatios = [];

        $renditions = $this->getRenditions($class, $field);

        /** @var RenditionDefinition $rendition */
        foreach ($renditions as $rendition) {
            if ($rendition->getMethod() !== RenditionDefinition::METHOD_THUMB) {
                $aspectRatios[] = $rendition->getAspectRatio();
            }
        }

        return array_unique($aspectRatios);
    }

    public function getAspectRatiosForComponent(string $class, string $field): array
    {
        $ratios = [];
        $aspectRatios = $this->getAspectRatios($class, $field);

        foreach ($aspectRatios as $aspectRatio) {
            if ($aspectRatio === null) {
                $ratios['_'] = [
                    'label' => '_',
                    'fraction' => null
                ];
            } else {
                $key = (string) str_replace(':', 'x', $aspectRatio);
                list($width, $height) = explode(':', $aspectRatio, 2);
                $ratios[$key] = [
                    'label' => $aspectRatio,
                    'fraction' => round(\intval($width) / \intval($height), 4, PHP_ROUND_HALF_UP)
                ];
            }
        }

        return $ratios;
    }

    public function getMinimumsForComponent(string $class, string $field): array
    {
        $minimums = [];

        $renditions = $this->getRenditions($class, $field);
        /** @var RenditionDefinition $rendition */
        foreach ($renditions as $rendition) {
            if ($rendition->getMethod() !== RenditionDefinition::METHOD_THUMB) {
                $aspectRatio = $rendition->getAspectRatio();
                if ($aspectRatio === null) {
                    $aspectRatio = '_';
                }

                $key = (string) str_replace(':', 'x', $aspectRatio);
                if (!isset($minimums[$key])) {
                    $minimums[$key] = [
                        'width' => null,
                        'height' => null,
                    ];
                }

                if ($rendition->getWidth() !== null && $rendition->getWidth() > $minimums[$key]['width']) {
                    $minimums[$key]['width'] = $rendition->getWidth();
                }
                if ($rendition->getHeight() !== null && $rendition->getHeight() > $minimums[$key]['height']) {
                    $minimums[$key]['height'] = $rendition->getHeight();
                }
            }
        }

        return $minimums;
    }

    public function registerRenderMethod(string $method, callable $function): void
    {
        $this->renderMethods[$method] = $function;
    }

    public function handle(
        object $entity,
        string $imageField,
        UploadedFile $imageFile,
        array $options = null,
        ?string $entityClass = null
    ): AssetImage {
        $this->processImageMetadata($imageFile);

        $assetImage = $this->assetImageService->createFromUploadedFile(
            $imageFile,
            $entity,
            $imageField,
            $this->storageService->getStorageMetadata()
        );

        $this->storageService->save(
            $this->getFilePath($assetImage, 'original'),
            file_get_contents($imageFile->getPathname()),
            StorageDriverInterface::CONTEXT_PUBLIC
        );

        if ($entityClass === null) {
            $entityClass = get_class($entity);
        }

        /** @var RenditionDefinition $definition */
        foreach ($this->getRenditions($entityClass, $imageField) as $definition) {
            $this->createRendition($assetImage, $definition, $options);
        }

        return $assetImage;
    }

    public function handleStandalone(UploadedFile $imageFile, array $options = null): AssetImage
    {
        $this->processImageMetadata($imageFile);

        $assetImage = $this->assetImageService->createFromUploadedFile(
            $imageFile,
            null,
            null,
            $this->storageService->getStorageMetadata()
        );

        $this->storageService->save(
            $this->getFilePath($assetImage, 'original'),
            file_get_contents($imageFile->getPathname()),
            StorageDriverInterface::CONTEXT_PUBLIC
        );

        $thumbRenditionWidth = 300;
        $thumbRenditionHeight = 300;
        $thumbRenditionSub = ['80x80', '150x150'];
        if (isset($options['renditions'])) {
            $thumbRendition = \array_filter($options['renditions'], fn (stdClass $rendition): bool => $rendition->name === 'thumb');
            if (!empty($thumbRendition)) {
                $thumbRenditionWidth = $thumbRendition[0]->width;
                $thumbRenditionHeight = $thumbRendition[0]->height;
                $thumbRenditionSub = [];
            }
        }

        $this->createRendition(
            $assetImage,
            new RenditionDefinition('', '', 'thumb', $thumbRenditionWidth, $thumbRenditionHeight, RenditionDefinition::METHOD_THUMB, $thumbRenditionSub),
            $options
        );

        if (isset($options['renditions'])){
            foreach ($options['renditions'] as $rendition) {
                if ('thumb' === $rendition->name) {
                    continue;
                }
                $this->createRendition(
                    $assetImage,
                    new RenditionDefinition('', '', $rendition->name, $rendition->width, $rendition->height, RenditionDefinition::METHOD_CROP,  [] ),
                    $options
                );
            }
        }

        return $assetImage;
    }

    public function copy(
        object $entity,
        string $imageField,
        int $assetId,
        array $options = null,
        ?string $entityClass = null
    ): ?AssetImage {
        $sourceAssetImage = $this->assetImageService->get($assetId);
        if (null === $sourceAssetImage) {
            return null;
        }
        $sourceAssetImageContent = $this->storageService->get(
            $this->getFilePath($sourceAssetImage, 'original'),
            StorageDriverInterface::CONTEXT_PUBLIC,
            $sourceAssetImage->getStorageMetadata()
        );

        $assetImage = $this->assetImageService->createFromAsset($entity, $imageField, $sourceAssetImage);

        if (null === $entityClass) {
            $entityClass = get_class($entity);
        }

        $this->storageService->save(
            $this->getFilePath($assetImage, 'original'),
            $sourceAssetImageContent,
            StorageDriverInterface::CONTEXT_PUBLIC
        );

        /** @var RenditionDefinition $definition */
        foreach ($this->getRenditions(\strval($entityClass), $imageField) as $definition) {
            $this->createRendition($assetImage, $definition, $options);
        }

        return $assetImage;
    }

    public function delete(object $entity, string $imageField): void
    {
        $fieldSetter = sprintf('set%s', ucfirst((string) $imageField));
        if (method_exists($entity, $fieldSetter)) {
            try {
                $ref = new \ReflectionMethod(\strval(get_class($entity)), $fieldSetter);
                $ref->invoke($entity, null);
            } catch (\ReflectionException $e) {
                $this->logger->error(sprintf('Error deleting AssetImage on Entity. Error: %s', $e->getMessage()));
            }
        }
    }

    public function getImageUrl(AssetImage $image, string $rendition, ?string $subRendition = null): string
    {
        return $this->storageService->getPublicUrl($this->getFilePath($image, $rendition, $subRendition), $image->getStorageMetadata());
    }

    public function dynamicResize(string $requestedFile, string $originalFile, int $width, int $height): Image
    {
        $originalContent = $this->storageService->get(sprintf('images/%s', $originalFile), StorageDriverInterface::CONTEXT_PUBLIC);
        $originalImage = $this->imageManager->make($originalContent);
        $originalImage->fit($width, $height);

        $this->storageService->save(sprintf('images/%s', $requestedFile), $originalImage->encode(null, 90), StorageDriverInterface::CONTEXT_PUBLIC);

        return $originalImage;
    }

    protected function getFilePath(AssetImage $assetImage, string $rendition, ?string $subRendition = null): string
    {
        if ($subRendition !== null) {
            return sprintf(
                'images/%d/%d/%s/%s/%s',
                floor($assetImage->getId() / 1000),
                $assetImage->getId(),
                $rendition,
                $subRendition,
                $assetImage->getFilename()
            );
        }

        return sprintf(
            'images/%d/%d/%s/%s',
            floor($assetImage->getId() / 1000),
            $assetImage->getId(),
            $rendition,
            $assetImage->getFilename()
        );
    }

    protected function createRendition(
        AssetImage $assetImage,
        RenditionDefinition $definition,
        array $options = null
    ): void {
        $imageContent = $this->storageService->get(
            $this->getFilePath($assetImage, 'original'),
            StorageDriverInterface::CONTEXT_PUBLIC,
            $assetImage->getStorageMetadata()
        );

        $image = $this->imageManager->make($imageContent);
        $image = $this->executeRenderMethod($definition, $image, $options);

        $this->storageService->save(
            $this->getFilePath($assetImage, $definition->getName()),
            $image->encode(null, $definition->getQuality()),
            StorageDriverInterface::CONTEXT_PUBLIC
        );

        foreach ($definition->getSubRenditions() as $subRendition) {
            list($subRenditionWidth, $subRenditionHeight) = explode('x', $subRendition, 2);
            $subRenditionImage = clone $image;

            // check image width is empty
            if ($subRenditionWidth <= 0) {
                $subRenditionWidth = floor(($subRenditionHeight / $image->getHeight()) * $image->getWidth());
            }

            // check image height is empty
            if ($subRenditionHeight <= 0) {
                $subRenditionHeight = floor(($subRenditionWidth / $image->getWidth()) * $image->getHeight());
            }

            $subRenditionImage->fit((int) $subRenditionWidth, (int) $subRenditionHeight);
            $this->storageService->save(
                $this->getFilePath($assetImage, $definition->getName(), $subRendition),
                $subRenditionImage->encode(null, $definition->getQuality()),
                StorageDriverInterface::CONTEXT_PUBLIC
            );
        }
    }

    protected function executeRenderMethod(RenditionDefinition $definition, Image $image, array $options = null): Image
    {
        if (isset($this->renderMethods[$definition->getMethod()])) {
            /** @var Image */
            return call_user_func_array($this->renderMethods[$definition->getMethod()], [$definition, $image, $options]);
        }

        throw new \Exception(sprintf('Render Method "%s" not registered.', $definition->getMethod()));
    }

    protected function renderMethodThumb(RenditionDefinition $definition, Image $image, ?array $options = null): Image
    {
        // If Crop data and thumb rendition are defined, crop it
        if (
            isset($options['cropper']) &&
            is_array($options['cropper']) &&
            count($options['cropper']) > 0
        ) {
            $key = $options['thumb-rendition'] ??  null;
            if (null !== $key && isset($options['cropper'][$key])) {
                list(
                    $cropData['w'],
                    $cropData['h'],
                    $cropData['x'],
                    $cropData['y']
                ) = explode(';', $options['cropper'][$key]);
                $image->crop(
                    (int) round((float) $cropData['w']),
                    (int) round((float) $cropData['h']),
                    (int) round((float) $cropData['x']),
                    (int) round((float) $cropData['y'])
                );
            }
        }

        // Image is not 1:1 or Thumb is not 1:1
        if (($image->getWidth() != $image->getHeight()) || ($definition->getWidth() != $definition->getHeight())) {
            $image->resize($definition->getWidth(), $definition->getHeight(), function (Constraint $constraint) {
                $constraint->aspectRatio();
            });

            if ($definition->getWidth() !== null && $definition->getHeight() !== null) {
                $image->resizeCanvas($definition->getWidth(), $definition->getHeight(), 'center', false, 'rgba(247, 247, 247, 1)');
            }

            // Image and Thumb are 1:1
        } else {
            if ($definition->getWidth() !== null && $definition->getHeight() !== null) {
                $image->fit($definition->getWidth(), $definition->getHeight());
            }
        }

        return $image;
    }

    protected function renderMethodCrop(RenditionDefinition $definition, Image $image, ?array $options = null): Image
    {
        $aspectRatio = $definition->getAspectRatio();
        if (null === $aspectRatio) {
            throw new \RuntimeException('Aspect ratio for crop not defined');
        }

        // If Crop data is defined, crop it
        $key = str_replace(':', 'x', $aspectRatio);
        if (isset($options['cropper']) && isset($options['cropper'][$key])) {
            list(
                $cropData['w'],
                $cropData['h'],
                $cropData['x'],
                $cropData['y']
            ) = explode(';', $options['cropper'][$key]);

            $image->crop(
                (int) round((float) $cropData['w']),
                (int) round((float) $cropData['h']),
                (int) round((float) $cropData['x']),
                (int) round((float) $cropData['y'])
            );
        }

        if ($definition->getWidth() !== null && $definition->getHeight() !== null) {
            $image->fit($definition->getWidth(), $definition->getHeight());
        }

        $image->interlace(false);

        return $image;
    }

    protected function processImageMetadata(UploadedFile $imageFile): void
    {
        $exifMetadata = @exif_read_data($imageFile);
        if (is_array($exifMetadata) && isset($exifMetadata['Orientation'])) {
            switch ($exifMetadata['Orientation']) {
                case 8:
                    $this->imageManager->make($imageFile->getPathname())
                        ->rotate(90)
                        ->save($imageFile->getPathname());
                    break;
                default:
                    break;
            }
        }
    }
}
