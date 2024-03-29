<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use WS\Core\Library\Traits\Entity\TimestampableTrait;

#[ORM\Entity]
#[ORM\Table(name: 'ws_translation_attribute')]
#[ORM\UniqueConstraint(columns: ['attrib_node', 'attrib_name'])]
class TranslationAttribute
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'attrib_id', type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: TranslationNode::class)]
    #[ORM\JoinColumn(name: 'attrib_node', referencedColumnName: 'node_id', nullable: false)]
    private TranslationNode $node;

    #[ORM\Column(name: 'attrib_name', type: 'string', length: 64, nullable: false)]
    private string $name;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'attrib_created_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $createdAt;

    #[Gedmo\Timestampable]
    #[ORM\Column(name: 'attrib_modified_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setNode(TranslationNode $node): self
    {
        $this->node = $node;

        return $this;
    }

    public function getNode(): ?TranslationNode
    {
        return $this->node;
    }
}
