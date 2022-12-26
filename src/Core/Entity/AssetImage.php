<?php

namespace WS\Core\Entity;

use WS\Core\Library\Traits\Entity\BlameableTrait;
use WS\Core\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'WS\Core\Repository\AssetImageRepository')]
#[ORM\Table(name: 'ws_asset_image')]
class AssetImage
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'image_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'image_filename', type: 'string', length: 128, nullable: false)]
    private string $filename;

    #[Assert\Length(max: 128)]
    #[Assert\NotBlank]
    #[ORM\Column(name: 'image_mime_type', type: 'string', length: 128, nullable: false)]
    private string $mimeType;

    #[ORM\Column(name: 'image_width', type: 'integer', nullable: true)]
    private ?int $width;

    #[ORM\Column(name: 'image_height', type: 'integer', nullable: true)]
    private ?int $height;

    #[ORM\Column(name: 'image_visible', type: 'boolean', nullable: false, options: ['default' => 1])]
    private bool $visible = true;

    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[Assert\Type('DateTime')]
    #[ORM\Column(name: 'image_created_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     */
    #[ORM\Column(name: 'image_modified_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $modifiedAt;

    /**
     * @Gedmo\Blameable(on="create")
     */
    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'image_created_by', type: 'string', length: 128, nullable: true)]
    private ?string $createdBy = null;

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

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }
}
