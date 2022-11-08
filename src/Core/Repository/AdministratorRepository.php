<?php

namespace WS\Core\Repository;

use WS\Core\Entity\Administrator;
use WS\Core\Library\CRUD\AbstractRepository;

/**
 * @method Administrator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Administrator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Administrator[]    findAll()
 * @method Administrator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministratorRepository extends AbstractRepository
{
    public function getEntityClass(): string
    {
        return Administrator::class;
    }
}
