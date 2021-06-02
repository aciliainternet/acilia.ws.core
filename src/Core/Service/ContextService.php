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

    public function setContext($context) : self
    {
        $this->context = $context;

        return $this;
    }

    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
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
    public function getDomains() : array
    {
        return $this->domainService->getCanonicals();
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
