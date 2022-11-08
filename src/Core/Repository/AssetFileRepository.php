<?php

namespace WS\Core\Repository;

use WS\Core\Entity\AssetFile;
use WS\Core\Entity\Domain;
use WS\Core\Library\CRUD\AbstractRepository;

/**
 * @method AssetFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetFile[]    findAll()
 * @method AssetFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method AssetFile[]    getAvailableByIds(?Domain $domain, array $ids): array
 */

class AssetFileRepository extends AbstractRepository
{
    public function getEntityClass(): string
    {
        return AssetFile::class;
    }
}
