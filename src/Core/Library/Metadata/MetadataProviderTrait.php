<?php

namespace WS\Core\Library\Metadata;

use WS\Core\Library\CRUD\AbstractService;

trait MetadataProviderTrait
{
    public function getMetadataSupportFor(): array
    {
        if ($this instanceof AbstractService) {
            return [$this->getEntityClass()];
        }

        return [];
    }

    public function getMetadataTitle(object $entity): ?string
    {
        if (method_exists($entity, 'getMetadataTitle')) {
            if (null !== $entity->getMetadataTitle()) {
                return $entity->getMetadataTitle();
            }
        }

        if (method_exists($entity, 'getTitle')) {
            return $entity->getTitle();
        }

        return null;
    }

    public function getMetadataDescription(object $entity): ?string
    {
        if (method_exists($entity, 'getMetadataDescription')) {
            if (null !== $entity->getMetadataDescription()) {
                return $entity->getMetadataDescription();
            }
        }

        if (method_exists($entity, 'getDescription')) {
            return $entity->getDescription();
        }

        return null;
    }

    public function getMetadataKeywords(object $entity): ?string
    {
        if (method_exists($entity, 'getMetadataKeywords')) {
            return $entity->getMetadataKeywords();
        }

        return null;
    }

    public function getOpenGraphTitle(object $entity): ?string
    {
        return $this->getMetadataTitle($entity);
    }

    public function getOpenGraphType(object $entity): ?string
    {
        return MetadataProviderInterface::OPEN_GRAPH_TYPE_WEBSITE;
    }

    public function getOpenGraphImage(object $entity): ?string
    {
        return null;
    }

    public function getOpenGraphImageType(object $entity): ?string
    {
        return null;
    }

    public function getOpenGraphImageWidth(object $entity): ?int
    {
        return null;
    }

    public function getOpenGraphImageHeight(object $entity): ?int
    {
        return null;
    }

    public function getOpenGraphVideo(object $entity): ?string
    {
        return null;
    }

    public function getOpenGraphVideoType(object $entity): ?string
    {
        return null;
    }

    public function getOpenGraphVideoWidth(object $entity): ?int
    {
        return null;
    }

    public function getOpenGraphVideoHeight(object $entity): ?int
    {
        return null;
    }
}
