<?php

namespace WS\Core\Library\Traits\Entity;

trait MetadataEntityTrait
{
    public function getMetadataTitle(): ?string
    {
        return $this->metadataTitle;
    }

    public function setMetadataTitle(?string $metadataTitle): self
    {
        $this->metadataTitle = $metadataTitle;

        return $this;
    }

    public function getMetadataDescription(): ?string
    {
        return $this->metadataDescription;
    }

    public function setMetadataDescription(?string $metadataDescription): self
    {
        $this->metadataDescription = $metadataDescription;

        return $this;
    }

    public function getMetadataKeywords(): ?string
    {
        return $this->metadataKeywords;
    }

    public function setMetadataKeywords(?string $metadataKeywords): self
    {
        $this->metadataKeywords = $metadataKeywords;

        return $this;
    }
}
