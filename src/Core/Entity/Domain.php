<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'WS\Core\Repository\DomainRepository')]
#[ORM\Table(name: 'ws_domain')]
#[ORM\UniqueConstraint(columns: ['domain_host', 'domain_locale'])]
class Domain
{
    public const CANONICAL = 'canonical';
    public const ALIAS = 'alias';
    public const REDIRECT = 'redirect';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'domain_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'domain_host', type: 'string', length: 64, nullable: false)]
    private string $host;

    #[ORM\Column(name: 'domain_locale', type: 'string', length: 2, nullable: false)]
    private string $locale;

    #[ORM\Column(name: 'domain_culture', type: 'string', length: 6, nullable: false)]
    private string $culture;

    #[ORM\Column(name: 'domain_timezone', type: 'string', length: 32, nullable: false)]
    private string $timezone;

    #[ORM\Column(name: 'domain_type', type: 'string', length: 12, nullable: false)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: 'WS\Core\Entity\Domain')]
    #[ORM\JoinColumn(name: 'domain_parent', referencedColumnName: 'domain_id', nullable: true)]
    private ?Domain $parent = null;

    #[ORM\Column(name: 'domain_default', type: 'smallint', nullable: false)]
    private int $default = 0;


    public function __toString(): string
    {
        return $this->host;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getCulture(): string
    {
        return $this->culture;
    }

    public function setCulture(string $culture): self
    {
        $this->culture = $culture;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParent(): ?Domain
    {
        return $this->parent;
    }

    public function setParent(?Domain $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function isCanonical(): bool
    {
        return $this->type === self::CANONICAL;
    }

    public function isAlias(): bool
    {
        return $this->type === self::ALIAS;
    }

    public function isDefault(): bool
    {
        return $this->default === 1;
    }

    public function setDefault(int $default): self
    {
        $this->default = $default;

        return $this;
    }
}
