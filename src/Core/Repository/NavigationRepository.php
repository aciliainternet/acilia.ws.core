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
}
