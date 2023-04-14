<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use WS\Core\Library\Traits\Entity\BlameableTrait;
use WS\Core\Library\Traits\Entity\TimestampableTrait;
use WS\Core\Repository\AssetFileRepository;

#[ORM\Entity(repositoryClass: AssetFileRepository::class)]
#[ORM\Table(name: 'ws_asset_file')]
class AssetFile
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'file_id', type: 'integer')]
    private ?int $id;

    #[Assert\Length(max: 128)]
    #[Assert\NotBlank]
    #[ORM\Column(name: 'file_filename', type: 'string', length: 128, nullable: false)]
    private string $filename;

    #[Assert\Length(max: 128)]
    #[Assert\NotBlank]
    #[ORM\Column(name: 'file_mime_type', type: 'string', length: 128, nullable: false)]
    private string $mimeType;

    #[ORM\Column(name: 'file_storage_metadata', type: 'json', nullable: true)]
    private array $storageMetadata = [];

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'file_created_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $createdAt;

    #[Gedmo\Timestampable]
    #[ORM\Column(name: 'file_modified_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $modifiedAt;

    #[Gedmo\Blameable(on: 'create')]
    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'file_created_by', type: 'string', length: 128, nullable: true)]
    private string $createdBy;

    public function __toString(): string
    {
        return \strval($this->id);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getStorageMetadata(): array
    {
        return $this->storageMetadata;
    }

    public function setStorageMetadata(array $storageMetadata): self
    {
        $this->storageMetadata = $storageMetadata;

        return $this;
    }
}
