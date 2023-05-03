<?php

namespace WS\Core\Library\Navigation;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface NavigationEntityInterface
{
    /**
     * Gets the entity fully qualified class name
     */
    static function getClassName(): string;

    /**
     * identifies the entity as displayed in the lists of entities
     * to be shown in the cms, example: 'Pages'
     */
    function getLabel(): string; 

    /**
     * Gets an entity that can be passed on to get its label and url
     * through this service
     */
    function getEntityById(int $id): NavigationEntityItemInterface;

    /**
     * Gets the label to be displayed by the menu item exposed to the site
     */
    function getEntityLabel(NavigationEntityItemInterface $entity): string;

    /**
     * Gets the Url to access the public controller that shows the entity
     */
    function getEntityUrl(NavigationEntityItemInterface $entity): string;
    
    /**
     * Gets the list of all entities of this class that can be added to a navigation
     * 
     * @return NavigationEntityItemInterface[]
     */
    function getNavigationEntities(): array;
}
