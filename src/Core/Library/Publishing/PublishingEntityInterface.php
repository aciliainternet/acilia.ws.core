<?php

namespace WS\Core\Library\Publishing;

interface PublishingEntityInterface
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_UNPUBLISHED = 'unpublished';
    public const STATUS_DRAFT = 'draft';

    public const FILTER_STATUS = 'ws_cms_publishing_status';

    public function getPublishStatus(): ?string;

    public function setPublishStatus(?string $publishStatus): self;

    public function getPublishSince(): ?\DateTimeInterface;

    public function setPublishSince(?\DateTimeInterface $publishSince): self;

    public function getPublishUntil(): ?\DateTimeInterface;

    public function setPublishUntil(?\DateTimeInterface $publishUntil): self;
}
