<?php

namespace WS\Core\Service;

use WS\Core\Entity\Domain;

interface ContextInterface
{
    public const CMS = 'cms';
    public const SITE = 'site';
    public const SYMFONY = 'symfony';
    public const SESSION_DOMAIN = 'ws_domain_id';

    public function setContext(string $context): self;

    public function setDomain(Domain $domain): void;

    public function getDomain(): ?Domain;

    public function getDomains(): array;

    public function getDomainByLocale(string $locale, string $type = Domain::CANONICAL): ?Domain;

    public function isCMS(): bool;

    public function isSite(): bool;

    public function getTemplatesBase(): string;
}
