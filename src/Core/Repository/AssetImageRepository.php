<?php

namespace WS\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use WS\Core\Entity\AssetImage;
use WS\Core\Entity\Domain;
use WS\Core\Library\CRUD\AbstractRepository;

/**
 * @method AssetImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetImage[]    findAll()
 * @method AssetImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method AssetImage[]    getAvailableByIds(Domain $domain, array $ids): array
 * @method AssetImage[]    getAll(Domain $domain, ?string $search, ?array $filter, array $orderBy = null, int $limit = null, int $offset = null)
 */

class AssetImageRepository extends AbstractRepository
{
    public function getEntityClass(): string
    {
        return AssetImage::class;
    }

    public function processFilterExtended(QueryBuilder $qb, ?array $filter): void
    {
         if (isset($filter['visible'])) {
            $qb
                ->andWhere($qb->getRootAliases()[0]. '.visible = :visible')
                ->setParameter('visible', $filter['visible']);
        }
    }
}
