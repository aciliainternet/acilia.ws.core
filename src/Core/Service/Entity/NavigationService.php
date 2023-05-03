<?php

namespace WS\Core\Service\Entity;

use WS\Core\Entity\Navigation;
use WS\Core\Library\CRUD\AbstractService;

class NavigationService extends AbstractService
{
    public function makeDefault(Navigation $navigation)
    {
        $this->resetDefaults();
        
        $navigation->setDefault(true);
        $this->em->persist($navigation);
        $this->em->flush();
    }

    public function resetDefaults()
    {
        $this->repository->resetDefaults();
    }

    public function getNavigationTree(Navigation $navigation): array
    {
        return [
            'entity' => $navigation,
            'children' => $this->getNavigationItems($navigation),
        ];
    }

    private function getNavigationItems(Navigation $navigation, ?int $parentId = null): array
    {
        $navigationItems = [];

        foreach ($navigation->getMenuItems() as $item) {
            if ((null === $item->getParent() && null === $parentId) || $item->getParent()->getId() === $parentId) {
                $navigationItems[] = [
                    'entity' => $item,
                    'children' => $this->getNavigationItems($navigation, $item->getId()),
                ];
            }
        }

        return $navigationItems;
    }
}
