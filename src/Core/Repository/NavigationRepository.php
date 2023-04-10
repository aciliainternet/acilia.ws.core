<?php

namespace WS\Core\Repository;

use WS\Core\Entity\Navigation;
use WS\Core\Library\CRUD\AbstractRepository;

class NavigationRepository extends AbstractRepository
{
    public function resetDefaults()
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.default', ':default')
            ->setParameter(':default', 0)
            ->getQuery()
            ->execute()
        ;
    }

    public function getByName(string $name): ?Navigation
    {
        return $this->createQueryBuilder('n')
            ->where('n.navigation_name = :name')
            ->setParameter(':name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getDefault(): ?Navigation
    {
        return $this->createQueryBuilder('n')
            ->where('n.navigation_default = 1')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
