<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use WS\Core\Library\Traits\Entity\BlameableTrait;
use WS\Core\Library\Traits\Entity\TimestampableTrait;
use WS\Core\Repository\AdministratorRepository;
use WS\Core\Library\Attribute\CRUD as WS;

#[ORM\Entity(repositoryClass: AdministratorRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'ws.administrator.email_already_exists')]
#[ORM\Table(name: 'ws_administrator')]
class Administrator implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'administrator_id', type: 'integer', nullable: false)]
    private ?int $id;

    #[WS\ListField]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'administrator_name', type: 'string', length: 128, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'administrator_salt', type: 'string', length: 32, nullable: false)]
    private string $salt;

    #[ORM\Column(name: 'administrator_password', type: 'string', length: 128, nullable: false)]
    private string $password;

    #[WS\ListField]
    #[Assert\NotBlank]
    #[Assert\Length(max: 127)]
    #[Assert\Email]
    #[ORM\Column(name: 'administrator_email', type: 'string', length: 127, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'administrator_active', type: 'boolean', nullable: false)]
    private ?bool $active = false;

    #[WS\ListField(filter: 'ws_cms_administrator_profile')]
    #[ORM\Column(name: 'administrator_profile', type: 'string', length: 32, nullable: false)]
    private string $profile;

    #[WS\ListField(isDate: true)]
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'administrator_created_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $createdAt;

    #[Gedmo\Timestampable]
    #[ORM\Column(name: 'administrator_modified_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $modifiedAt;

    #[Gedmo\Blameable(on: 'create')]
    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'administrator_created_by', type: 'string', length: 128, nullable: true)]
    private ?string $createdBy = null;

    public function __construct()
    {
        $this->salt = substr(base_convert(sha1(uniqid((string) mt_rand(), true)), 16, 36), 0, 32);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(string $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_CMS', $this->profile];
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): \DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
