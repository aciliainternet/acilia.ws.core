<?php

namespace WS\Core\Library\Domain;

use Doctrine\ORM\QueryBuilder;
use WS\Core\Entity\Domain;

trait DomainRepositoryTrait
{
    protected function setDomainRestriction(string $alias, QueryBuilder $qb, ?Domain $domain): void
    {
        $class = class_implements($this->getClassName());
        if (false !== $class) {
            if (in_array(DomainDependantInterface::class, $class)) {
                $qb->andWhere(sprintf('%s.domain = :domain', $alias));
                $qb->setParameter('domain', $domain);
            }
        }
    }
}
