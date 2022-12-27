<?php

namespace WS\Core\Library\Domain;

use WS\Core\Entity\Domain;

interface DomainDependantInterface
{
    public function getDomain(): ?Domain;

    public function setDomain(?Domain $domain): DomainDependantInterface;
}
