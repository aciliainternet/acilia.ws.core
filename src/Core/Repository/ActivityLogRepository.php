<?php

namespace WS\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use WS\Core\Entity\ActivityLog;
use WS\Core\Entity\Domain;

/**
 * @method ActivityLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityLog[]    findAll()
 * @method ActivityLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityLog::class);
    }

    protected function setFilters(array $filters, QueryBuilder $qb): void
    {
        //Set filters
        if (isset($filters['model_id'])) {
            $qb->andWhere('t.modelId = :model_id')
                ->setParameter('model_id', $filters['model_id']);
        }

        if (isset($filters['model'])) {
            $qb->andWhere('t.model = :model')
                ->setParameter('model', $filters['model']);
        }

        if (isset($filters['user'])) {
            $qb->andWhere('t.createdBy = :user')
                ->setParameter('user', $filters['user']);
        }
    }

    public function getAll(?Domain $domain, array $filters, int $limit = null, int $offset = null): array
    {
        $alias = 't';
        $qb = $this->createQueryBuilder($alias);

        $qb->andWhere(sprintf('(%s.domain = :domain OR %s.domain IS NULL)', $alias, $alias));
        $qb->setParameter('domain', $domain);

        $this->setFilters($filters, $qb);

        $qb->orderBy(sprintf('%s.id', $alias), 'DESC');

        if (isset($limit) && isset($offset)) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
        }
        /** @var array */
        return $qb->getQuery()->execute();
    }

    public function getAllCount(?Domain $domain, array $filters): int
    {
        $alias = 't';
        $qb = $this->createQueryBuilder($alias)->select(sprintf(sprintf('count(%s.id)', $alias)));

        $qb->andWhere(sprintf('(%s.domain = :domain OR %s.domain IS NULL)', $alias, $alias));
        $qb->setParameter('domain', $domain);

        $this->setFilters($filters, $qb);

        try {
            /** @var int */
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
