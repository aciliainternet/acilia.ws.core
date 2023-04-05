<?php

namespace WS\Core\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use WS\Core\Library\Attribute\CRUD\ListField;
use WS\Core\Repository\NavigationRepository;

#[ORM\Entity(repositoryClass: NavigationRepository::class)]
#[ORM\Table(name: 'ws_navigation')]
#[ORM\UniqueConstraint(columns: ['navigation_name'])]
class Navigation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'navigation_id', type: Types::INTEGER)]
    private ?int $id;

    #[Assert\NotNull]
    #[ListField()]
    #[ORM\Column(name: 'navigation_name', type: Types::STRING, length: 64, nullable: false)]
    private string $name;

    #[ListField()]
    #[ORM\Column(name: 'navigation_default', type: Types::BOOLEAN, nullable: false)]
    private bool $default;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName():  string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function getDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): self
    {
        $this->default = $default;

        return $this;
    }
}
