<?php

namespace WS\Core\Library\Domain;

use WS\Core\Entity\Domain;

trait DomainDependantTrait
{
    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(?Domain $domain): DomainDependantInterface
    {
        $this->domain = $domain;

        return $this;
    }
}
