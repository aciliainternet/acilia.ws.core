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

    #[\Override]
    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    #[\Override]
    public function setDomain(Domain $domain): void
    {
        $this->domain = $domain;

        \Locale::setDefault(\strval(\str_replace('_', '-', $domain->getCulture())));
    }

    #[\Override]
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
    #[\Override]
    public function getDomains(): array
    {
        return $this->domainService->getCanonicals();
    }

    #[\Override]
    public function getDomainByLocale(string $locale, string $type = Domain::CANONICAL): ?Domain
    {
        $domains = \array_filter($this->getDomains(), fn ($d) => $d->getType() === $type && $locale === $d->getLocale());

        return \array_shift($domains);
    }

    #[\Override]
    public function isCMS(): bool
    {
        return $this->context === ContextInterface::CMS;
    }

    #[\Override]
    public function isSite(): bool
    {
        return $this->context === ContextInterface::SITE;
    }

    #[\Override]
    public function getTemplatesBase(): string
    {
        return $this->context === ContextInterface::CMS ? 'cms' : 'site';
    }
}
