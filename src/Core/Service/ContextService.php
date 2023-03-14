<?php

namespace WS\Core\Service;

use WS\Core\Entity\Domain;

final class ContextService implements ContextInterface
{
    protected string $context = '';
    protected ?Domain $domain = null;

    public function __construct(protected DomainInterface $domainService)
    {
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function setDomain(Domain $domain): void
    {
        $this->domain = $domain;

        \Locale::setDefault(\strval(\str_replace('_', '-', $domain)));
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
        $domains = \array_filter($this->getDomains(), fn ($d) => $d->getType() === $type && $locale === $d->getLocale());

        return \array_shift($domains);
    }

    public function isCMS(): bool
    {
        return $this->context === ContextInterface::CMS;
    }

    public function isSite(): bool
    {
        return $this->context === ContextInterface::SITE;
    }

    public function getTemplatesBase(): string
    {
        return $this->context === ContextInterface::CMS ? 'cms' : 'site';
    }
}
