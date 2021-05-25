<?php

namespace WS\Core\Entity;

use WS\Core\Library\Traits\Entity\BlameableTrait;
use WS\Core\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="WS\Core\Repository\AdministratorRepository")
 * @ORM\Table(name="ws_administrator")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="ws.administrator.email_already_exists"
 * )
 */
class Administrator implements UserInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(name="administrator_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     *
     * @ORM\Column(name="administrator_name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="administrator_salt", type="string", length=32, nullable=false)
     */
    protected $salt;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="administrator_password", type="string", length=128, nullable=false)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=127)
     * @Assert\Email()
     *
     * @ORM\Column(name="administrator_email", type="string", length=127, unique=true, nullable=false)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="administrator_active", type="boolean", nullable=false)
     */
    private $active = false;

    /**
    * @ORM\Column(name="administrator_profile", type="string", length=32, nullable=false)
    */
    private $profile;

    /**
     * @Assert\Type(type="\DateTime")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="administrator_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @Assert\Type(type="\DateTime")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="administrator_modified_at", type="datetime", nullable=false)
     */
    private $modifiedAt;

    /**
     * @Assert\Length(max=128)
     * @Gedmo\Blameable(on="create")
     * 
     * @ORM\Column(name="administrator_created_by", type="string", length=128, nullable=true)
     */
    private $createdBy;

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

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPassword()
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

    public function eraseCredentials()
    {
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
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
