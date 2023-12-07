<?php

namespace WS\Core\Library\Preview;

trait PreviewTrait
{
    public function getPreviewClassName(): string
    {
        return $this->getEntityClass();
    }

    public function getPreviewQuery(): ?string
    {
        return null;
    }

    public function getPreviewPath(array $options = []): ?string
    {
        return null;
    }
}
