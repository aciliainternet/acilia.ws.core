<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ws_translation_node", uniqueConstraints={@ORM\UniqueConstraint(columns={"node_name"})})
 */
class TranslationNode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="node_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="node_name", type="string", length=32, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="node_source", type="string", length=12, nullable=true)
     */
    private ?string $source = null;

    /**
     * @ORM\Column(name="node_type", type="string", length=12, nullable=false)
     */
    private string $type;

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

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSourcePath(): string
    {
        $source = '';
        if ($this->source) {
            $source = $this->source . '.';
        }

        return $source;
    }
}
