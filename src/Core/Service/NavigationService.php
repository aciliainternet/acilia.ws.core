<?php

namespace WS\Core\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use WS\Core\Library\Navigation\NavigationEntitiesModel;
use WS\Core\Library\Navigation\NavigationEntityInterface;
use WS\Core\Library\Navigation\NavigationEntityItemInterface;
use WS\Core\Library\Navigation\NavigationItemModel;
use WS\Core\Repository\NavigationRepository;

class NavigationService
{
    public function __construct(
        #[TaggedIterator(NavigationEntityInterface::class)]
        private iterable $serviceIterator,
        #[TaggedLocator(NavigationEntityInterface::class, defaultIndexMethod: 'getClassName')]
        private ServiceLocator $serviceLocator,
        private NavigationRepository $navigationRepository
    ) {
    }

    /** 
     * Gets a navigation model object with all the items in the menu
     * in a given depth. depth 0 will get all the tree
     *
     * @return NavigationItemModel[]
     **/
    public function getNavigationByName(string $name, int $depth = 0): array
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
    public function getDefaultNavigation(int $depth = 0): array
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
    public function getNavigationEntities(): array
    {
        $out = [];

        foreach ($this->serviceIterator as $service)
        {
            $out[] = (new NavigationEntitiesModel())
                ->setName($service->getLabel())
                ->setItems(new ArrayCollection($service->getNavigationEntities()));
        }

        return $out;
    }

    public function getNavigationEntityLabel(NavigationEntityItemInterface $entity): string
    {
        if (!$this->serviceLocator->has($entity::class)) {
            throw new \Exception(
                sprintf('Navigation service for entity %s not found!', $entity::class)
            );
        }

        /** @var NavigationEntityInterface */
        $service = $this->serviceLocator->get($entity::class);

        return $service->getEntityLabel($entity);
    }
}
