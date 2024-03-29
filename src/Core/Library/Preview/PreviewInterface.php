<?php

namespace WS\Core\Library\Preview;

interface PreviewInterface
{
    public function getPreviewClassName(): string;

    public function getPreviewPath(array $options = []): ?string;
}
