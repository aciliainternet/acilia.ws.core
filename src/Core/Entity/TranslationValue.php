<?php

namespace WS\Core\Entity;

use WS\Core\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'ws_translation_value')]
#[ORM\UniqueConstraint(name: 'unq_ws_translation_value', columns: ['value_domain', 'value_attribute'])]
class TranslationValue
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'value_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'WS\Core\Entity\TranslationAttribute')]
    #[ORM\JoinColumn(name: 'value_attribute', referencedColumnName: 'attrib_id', nullable: false)]
    private TranslationAttribute $attribute;

    #[ORM\Column(name: 'value_translation', type: 'text', nullable: true)]
    private ?string $translation = null;

    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(name: 'value_created_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     */
    #[ORM\Column(name: 'value_modified_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $modifiedAt;

    #[ORM\ManyToOne(targetEntity: 'WS\Core\Entity\Domain')]
    #[ORM\JoinColumn(name: 'value_domain', referencedColumnName: 'domain_id', nullable: false)]
    protected Domain $domain;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTranslation(?string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function setAttribute(TranslationAttribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getAttribute(): ?TranslationAttribute
    {
        return $this->attribute;
    }

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    public function setDomain(Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }
}
