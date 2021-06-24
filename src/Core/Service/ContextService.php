<?php

namespace WS\Core\Service;

use WS\Core\Entity\Domain;

class ContextService
{
    const CMS = 'cms';
    const SITE = 'site';
    const SYMFONY = 'symfony';
    const SESSION_DOMAIN = 'ws_domain_id';

    protected $debug;
    protected $domainService;
    protected $context;
    /** @var Domain */
    protected $domain;

    public function __construct($debug, DomainService $domainService)
    {
        $this->debug = $debug;
        $this->domainService = $domainService;
    }

    public function setContext($context): self
    {
        $this->context = $context;

        return $this;
    }

    public function setDomain(Domain $domain): void
    {
        $this->domain = $domain;

        \Locale::setDefault(str_replace('_', '-', $domain));
    }

    public function getDomain(): ?Domain
    {
        if ($this->domain instanceof Domain) {
            if ($this->domain->getType() === Domain::ALIAS) {
                return $this->domain->getParent();
            }

            return $this->domain;
        }

        return null;
    }

    /**
     * @return Domain[]
     */
    public function getDomains(): array
    {
        return $this->domainService->getCanonicals();
    }

    public function getDomainByLocale(string $locale, string $type = Domain::CANONICAL): ?Domain
    {
        $domains = \array_filter($this->getDomains(), function ($d) use ($locale, $type) {
            return $d->getType() === $type && $locale === $d->getLocale();
        });

        return \array_shift($domains);
    }

    public function isDebug() : bool
    {
        return $this->debug;
    }

    public function isCMS() : bool
    {
        return $this->context == self::CMS;
    }

    public function isSite() : bool
    {
        return $this->context == self::SITE;
    }

    public function getTemplatesBase() : string
    {
        return $this->context == self::CMS ? 'cms': 'site';
    }
}
