<?php

namespace WS\Core\Library\Navigation;

interface NavigationEntityItemInterface
{
    /**
     * Retrieves the entity id to be used inside the cms navigation
     * section
     */
    function getId(): ?int;
}
