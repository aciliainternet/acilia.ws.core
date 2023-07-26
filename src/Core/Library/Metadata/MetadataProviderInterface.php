<?php

namespace WS\Core\Library\Metadata;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag()]
interface MetadataProviderInterface
{
    const OPEN_GRAPH_TYPE_WEBSITE = 'website';
    const OPEN_GRAPH_TYPE_ARTICLE = 'article';
    const OPEN_GRAPH_TYPE_EVENT = 'event';

    public function getMetadataSupportFor(): array;

    public function getMetadataTitle(object $entity): ?string;

    public function getMetadataDescription(object $entity): ?string;

    public function getMetadataKeywords(object $entity): ?string;

    public function getOpenGraphTitle(object $entity): ?string;

    public function getOpenGraphType(object $entity): ?string;

    public function getOpenGraphImage(object $entity): ?string;

    public function getOpenGraphImageType(object $entity): ?string;

    public function getOpenGraphImageWidth(object $entity): ?int;

    public function getOpenGraphImageHeight(object $entity): ?int;

    public function getOpenGraphVideo(object $entity): ?string;

    public function getOpenGraphVideoType(object $entity): ?string;

    public function getOpenGraphVideoWidth(object $entity): ?int;

    public function getOpenGraphVideoHeight(object $entity): ?int;
}
