<?php

namespace WS\Core\Library\CRUD;

use Doctrine\ORM\NoResultException;
use WS\Core\Entity\Domain;
use WS\Core\Library\Domain\DomainRepositoryTrait;
use WS\Core\Service\ContextService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    use DomainRepositoryTrait;

    protected $contextService;

    public function __construct(ContextService $contextService, ManagerRegistry $registry)
    {
        $this->contextService = $contextService;

        parent::__construct($registry, $this->getEntityClass());
    }

    abstract public function getEntityClass();

    abstract public function getFilterFields();

    public function processFilterExtended(QueryBuilder $qb, ?array $filter)
    {
    }

    /**
     * @param Domain $domain
     * @param string|null $search
     * @param array|null $filter
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return int|mixed|string
     */
    public function getAll(
        Domain $domain,
        ?string $search,
        ?array $filter,
        array $orderBy = null,
        int $limit = null,
        int $offset = null
    ) {
        $qb = $this->getAllQueryBuilder();
        $alias = $qb->getRootAliases()[0];

        $this->setFilter($alias, $qb, $search);

        if ($orderBy && count($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $qb->addOrderBy(sprintf('%s.%s', $alias, $field), $dir);
            }
        }

        if (isset($limit) && isset($offset)) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
        }

        $this->processFilterExtended($qb, $filter);

        $this->setDomainRestriction($alias, $qb, $domain);

        return $qb->getQuery()->execute();
    }

    public function getAvailableByIds(Domain $domain, array $ids): array
    {
        $alias = 't';
        $qb = $this->createQueryBuilder($alias)
            ->where(sprintf('%s.id IN (:ids)', $alias))
            ->setParameter('ids', $ids);

        $this->setDomainRestriction($alias, $qb, $domain);

        return $qb->getQuery()->execute();
    }

    /**
     * @param Domain $domain
     * @param string|null $search
     * @param array|null $filter
     *
     * @return int
     */
    public function getAllCount(Domain $domain, ?string $search, ?array $filter)
    {
        $qb = $this->getAllCountQueryBuilder();
        $alias = $qb->getRootAliases()[0];

        $qb = $qb->select(sprintf(sprintf('count(%s.id)', $alias)));

        $this->setFilter($alias, $qb, $search);

        $this->processFilterExtended($qb, $filter);

        $this->setDomainRestriction($alias, $qb, $domain);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param string $alias
     * @param QueryBuilder $qb
     * @param string|null $search
     */
    protected function setFilter(string $alias, QueryBuilder $qb, ?string $search)
    {
        if (!$search) {
            return;
        }

        $filterConditions = [];
        $filterParameters = [];
        foreach ($this->getFilterFields() as $field) {
            $filterConditions[] = sprintf('%s LIKE :%s_filter', sprintf('%s.%s', $alias, $field), $field);
            $filterParameters[sprintf('%s_filter', $field)] = sprintf('%%%s%%', $search);
        }

        if (count($filterConditions) > 0) {
            $qb->andWhere(implode(' OR ', $filterConditions));
            foreach ($filterParameters as $key => $val) {
                $qb->setParameter($key, $val);
            }
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param array $filters
     */
    protected function setFilters(string $alias, QueryBuilder $qb, array $filters)
    {
        foreach ($filters as $field => $value) {
            $qb->andWhere(sprintf('%s LIKE :%s_filter', sprintf('%s.%s', $alias, $field), $field));
            $qb->setParameter(sprintf('%s_filter', $field), sprintf('%%%s%%', $value));
        }
    }

    /**
     * @param array $ids
     */
    public function batchDelete(array $ids)
    {
        $alias = 't';

        $qb = $this->createQueryBuilder($alias)
            ->delete($this->getEntityClass(), $alias)
            ->where(sprintf('%s.id IN (:ids)', $alias))
            ->setParameter('ids', $ids);

        $qb->getQuery()->execute();
    }

    protected function getAllQueryBuilder(): QueryBuilder
    {
        $alias = 't';
        return $this->createQueryBuilder($alias);
    }

    protected function getAllCountQueryBuilder(): QueryBuilder
    {
        $alias = 't';
        return $this->createQueryBuilder($alias);
    }
}
