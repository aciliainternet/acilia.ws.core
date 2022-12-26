<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use WS\Core\Library\Domain\DomainDependantInterface;
use WS\Core\Library\Domain\DomainDependantTrait;

#[ORM\Entity(repositoryClass: 'WS\Core\Repository\ActivityLogRepository')]
#[ORM\Table(name: 'ws_activity_log', options: ['collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'engine' => 'InnoDB'])]
class ActivityLog implements DomainDependantInterface
{
    use DomainDependantTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'activity_log_id')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'WS\Core\Entity\Domain')]
    #[ORM\JoinColumn(name: 'activity_log_domain', referencedColumnName: 'domain_id', nullable: true)]
    private ?Domain $domain = null;

    #[Assert\Length(max: 128)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 128, name: 'activity_log_model', nullable: false)]
    private string $model;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'integer', name: 'activity_log_model_id', nullable: false)]
    private int $modelId;

    #[Assert\Length(max: 128)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 128, name: 'activity_log_action', nullable: false)]
    private string $action;

    #[ORM\Column(type: 'json', name: 'activity_log_changes', nullable: true)]
    private array $changes;

    private ?array $parsedChanges = null;

    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(name: 'activity_log_created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Blameable(on="create")
     */
    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'activity_log_created_by', type: 'string', length: 128, nullable: true)]
    private ?string $createdBy = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModelId(int $modelId): self
    {
        $this->modelId = $modelId;

        return $this;
    }

    public function getModelId(): int
    {
        return $this->modelId;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setChanges(array $changes): self
    {
        $this->changes = $changes;

        return $this;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function setParsedChanges(?array $changes): self
    {
        $this->parsedChanges = $changes;

        return $this;
    }
    public function getParsedChanges(): ?array
    {
        return $this->parsedChanges;
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
