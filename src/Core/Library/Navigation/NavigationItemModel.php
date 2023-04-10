<?php

namespace WS\Core\Library\Navigation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class NavigationItemModel
{
    private int $id;

    private ?int $parentId;

    /**
     * Can be custom or must be returned by the getNavigationEntities() function
     */
    private string $label;
    
    /**
     * Can be custom or must be returned by the getNavigationEntities() function
     */
    private string $url;

    /**
     * If we are at the depth limit, we need information to know if there are elements beyond this point
     */
    private bool $hasChildren; 

    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getHasChildren(): bool
    {
        return $this->hasChildren;
    }

    public function setHasChildren(bool $hasChildren): self
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }
 
    /**
     * @return NavigationItemModel[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren(Collection $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function addItem(NavigationItemModel $item): self
    {
        if (!$this->children->contains($item)) {
            $this->children[] = $item;
        }

        return $this;
    }

    public function removeMenuItem(NavigationItemModel $item): self
    {
        $this->children->remove($item);
        return $this;
    }
}