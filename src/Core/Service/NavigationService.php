<?php

namespace WS\Core\Service;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use WS\Core\Library\Navigation\NavigationEntitiesModel;
use WS\Core\Library\Navigation\NavigationItemModel;
use WS\Core\Repository\NavigationRepository;

class NavigationService
{
    public function __construct(
        #[TaggedIterator(NavigationEntityInterface::class)]
        private iterable $serviceIterator,
        private NavigationRepository $navigationRepository
    ) {
    }

    /** 
     * Gets a navigation model object with all the items in the menu
     * in a given depth. depth 0 will get all the tree
     *
     * @return NavigationItemModel[]
     **/
    function getNavigationByName(string $name, int $depth = 0): array
    {
        $navigation = $this->navigationRepository->getByName($name);

        if (null === $navigation) {
            throw new \Exception(sprintf("Unable to find navigation by name %s", $name));
        }

        return $this->getForDepth($navigation->getMenuItems(), $depth);
    }

    /** 
     * @return NavigationItemModel[] 
     **/
    function getDefaultNavigation(int $depth = 0): array
    {
        $navigation = $this->navigationRepository->getDefault();

        if (null === $navigation) {
            throw new \Exception("Unable to find default navigation have you configured it?");
        }

        return $this->getForDepth($navigation->getMenuItems(), $depth);
    }

    private function getForDepth(Collection $items, int $depth): array
    {
        $out = [];

        $curDepth = 0;
        $curParent = null;

        while ($curDepth < $depth) {
            $curDepth++;
        }

        return $out;
    }

    /**
     * retrieves a list of all entities that can be used in a navigation
     * 
     * @return NavigationEntitiesModel[]
     **/
    function getNavigationEntities(): array
    {
        $out = [];

        foreach ($this->serviceIterator as $service)
        {
            $out[] = (new NavigationEntitiesModel())
                ->setName($service->getLabel())
                ->setItems($service->getNavigationEntities());
        }

        return $out;
    }
}
