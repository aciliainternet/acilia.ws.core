<?php

namespace WS\Core\Service;

use WS\Core\Entity\Domain;

interface DomainInterface
{
    public function getDomains(): array;

    public function create(Domain $domain): Domain;

    public function get(int $id): ?Domain;

    public function getByHost(string $host): array;

    public function getCanonicals(): array;

    public function getAliases(): array;
}
