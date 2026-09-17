<?php

namespace WS\Core\Library\Publishing;

interface PublishingEntityInterface
{
    public const string STATUS_PUBLISHED = 'published';
    public const string STATUS_UNPUBLISHED = 'unpublished';
    public const string STATUS_DRAFT = 'draft';

    public const string FILTER_STATUS = 'ws_cms_publishing_status';

    public function getPublishStatus(): ?string;

    public function setPublishStatus(?string $publishStatus): self;

    public function getPublishSince(): ?\DateTimeInterface;

    public function setPublishSince(?\DateTimeInterface $publishSince): self;

    public function getPublishUntil(): ?\DateTimeInterface;

    public function setPublishUntil(?\DateTimeInterface $publishUntil): self;
}
