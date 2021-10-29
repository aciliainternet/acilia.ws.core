<?php

namespace WS\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WS\Core\Entity\Domain;

/**
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
    }

    public function getAll(): array
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
