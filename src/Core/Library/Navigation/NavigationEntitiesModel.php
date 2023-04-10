<?php

namespace WS\Core\Library\Navigation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class NavigationEntitiesModel
{
    private string $name;

    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();   
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return NavigationEntityItemInterface[]
     */ 
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(NavigationEntityItemInterface $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    public function removeMenuItem(NavigationEntityItemInterface $item): self
    {
        $this->items->remove($item);
        return $this;
    }
}