<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: NavigationRepository::class)]
#[ORM\Table(name: 'ws_navigation_item')]
class NavigationItem
{
    const NAVIGATION_TYPE_ENTITY = 'entity';
    const NAVIGATION_TYPE_CUSTOM = 'custom';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'navigation_item_id', type: Types::INTEGER)]
    private ?int $id;

    #[ORM\Column(name: 'navigation_item_type', type: Types::STRING, length: 6, nullable: false)]
    private string $type;

    #[ORM\Column(name: 'navigation_item_label', type: Types::STRING, length: 128, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(name: 'navigation_item_url', type: Types::STRING, length: 256, nullable: true)]
    private ?string $customUrl = null;

    #[ORM\Column(name: 'navigation_item_class_name', type: Types::STRING, length: 128, nullable: true)]
    private ?string $className = null;

    #[ORM\Column(name: 'navigation_item_entity_id', type: Types::INTEGER, nullable: true)]
    private ?int $entityId = null;

    #[ORM\ManyToOne(targetEntity: Navigation::class, inversedBy: 'menuItems')]
    #[ORM\JoinColumn(name: 'navigation_item_navigation', referencedColumnName: 'navigation_id')]
    private Navigation $navigation;

    #[ORM\ManyToOne(targetEntity: NavigationItem::class)]
    #[ORM\JoinColumn(name: 'navigation_item_parent', referencedColumnName: 'navigation_item_id')]
    private ?NavigationItem $parent;

    #[ORM\Column(name: 'navigation_item_order', type: Types::INTEGER, nullable: false)]
    private int $navigationOrder;

    #[ORM\Column(name: 'navigation_item_visible', type: Types::BOOLEAN, nullable: false)]
    private bool $navigationVisible = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (self::NAVIGATION_TYPE_CUSTOM !== $type && self::NAVIGATION_TYPE_ENTITY !== $type) {
            throw new \Exception('Invalid Navigation Item Type.');
        }

        $this->type = $type;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCustomUrl(): ?string
    {
        return $this->customUrl;
    }

    public function setCustomUrl(?string $customUrl): self
    {
        $this->customUrl = $customUrl;

        return $this;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(?string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getNavigation(): Navigation
    {
        return $this->navigation;
    }

    public function setNavigation(Navigation $navigation): self
    {
        $this->navigation = $navigation;

        return $this;
    }

    public function getParent(): ?NavigationItem
    {
        return $this->parent;
    }

    public function setParent(?NavigationItem $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getNavigationOrder(): int
    {
        return $this->navigationOrder;
    }

    public function setNavigationOrder(int $navigationOrder): self
    {
        $this->navigationOrder = $navigationOrder;

        return $this;
    }

    public function getNavigationVisible(): bool
    {
        return $this->navigationVisible;
    }

    public function isNavigationVisible(): bool
    {
        return $this->navigationVisible;
    }

    public function setNavigationVisible(bool $navigationVisible): self
    {
        $this->navigationVisible = $navigationVisible;

        return $this;
    }
}
