<?php

namespace WS\Core\Library\Preview;

trait PreviewTrait
{
    public function getPreviewClassName(): string
    {
        return $this->getEntityClass();
    }
}
