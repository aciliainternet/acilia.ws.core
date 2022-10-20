<?php

namespace WS\Core\Repository;

use Doctrine\ORM\NoResultException;
use WS\Core\Entity\ActivityLog;
use WS\Core\Entity\Domain;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * @method ActivityLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityLog[]    findAll()
 * @method ActivityLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityLogRepository extends EntityRepository
{
    protected function setFilters(array $filters, QueryBuilder &$qb)
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
            $qb->andWhere('t.createdBy LIKE :user')
                ->setParameter('user', sprintf('%%%s%%', $filters['user']));
        }
    }

    /**
     * @param Domain $domain
     * @param array $filters
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return mixed
     */
    public function getAll(Domain $domain, array $filters, int $limit = null, int $offset = null)
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

        return $qb->getQuery()->execute();
    }

    public function getAllCount(Domain $domain, array $filters)
    {
        $alias = 't';
        $qb = $this->createQueryBuilder($alias)->select(sprintf(sprintf('count(%s.id)', $alias)));

        $qb->andWhere(sprintf('(%s.domain = :domain OR %s.domain IS NULL)', $alias, $alias));
        $qb->setParameter('domain', $domain);

        $this->setFilters($filters, $qb);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
