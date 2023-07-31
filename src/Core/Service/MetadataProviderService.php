<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use WS\Core\Library\Metadata\MetadataProviderInterface;

class MetadataProviderService
{
    private array $supported = [];

    public function __construct(
        #[TaggedLocator(MetadataProviderInterface::class)]
        private ServiceLocator $providers,
    ) {
        foreach ($this->providers->getProvidedServices() as $providerId => $providerClass) {
            foreach ($this->providers->get($providerId)->getMetadataSupportFor() as $service) {
                $this->supported[$service] = $this->providers->get($providerId);
            }
        }
    }

    public function isSupported(object $entity): bool
    {
        return \in_array($entity::class, array_keys($this->supported));
    }

    public function getTitle(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getMetadataTitle($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getDescription(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getMetadataDescription($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getKeywords(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getMetadataKeywords($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphTitle(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getOpenGraphTitle($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphType(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getOpenGraphType($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphImage(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getOpenGraphImage($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphImageType(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getOpenGraphImageType($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphImageWidth(object $entity): ?int
    {
        try {
            return $this->getProvider($entity)->getOpenGraphImageWidth($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphImageHeight(object $entity): ?int
    {
        try {
            return $this->getProvider($entity)->getOpenGraphImageHeight($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphVideo(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getOpenGraphVideo($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphVideoType(object $entity): ?string
    {
        try {
            return $this->getProvider($entity)->getOpenGraphVideoType($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphVideoWidth(object $entity): ?int
    {
        try {
            return $this->getProvider($entity)->getOpenGraphVideoWidth($entity);
        } catch (\Exception) {
        }

        return null;
    }

    public function getOpenGraphVideoHeight(object $entity): ?int
    {
        try {
            return $this->getProvider($entity)->getOpenGraphVideoHeight($entity);
        } catch (\Exception) {
        }

        return null;
    }

    private function getProvider(object $entity): MetadataProviderInterface
    {
        if (\in_array($entity::class, array_keys($this->supported))) {
            return $this->supported[$entity::class];
        }

        throw new \RuntimeException('Invalid MetadataProvider');
    }
}
