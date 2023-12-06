<?php

namespace WS\Core\Library\Preview;

interface PreviewInterface
{
    public function getPreviewClassName(): string;

    public function getPreviewQuery(): ?string;

    public function getPreviewPath(array $options = []): ?string;
}
