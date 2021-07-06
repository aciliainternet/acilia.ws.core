<?php

namespace WS\Core\Repository;

use WS\Core\Entity\Domain;
use Doctrine\ORM\EntityRepository;

/**
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends EntityRepository
{
    public function getAll()
    {
        $alias = 'd';
        $qb = $this->createQueryBuilder($alias);

        // fetch parent to avoid extra query
        $qb->leftJoin(sprintf('%s.parent', $alias), 'parent');

        // order by default domain
        $qb->orderBy(sprintf('%s.default', $alias), 'DESC');

        return $qb->getQuery()->execute();
    }
}
