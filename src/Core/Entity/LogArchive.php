<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ws_log_archive', options: ['collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'engine' => 'InnoDB'])]
class LogArchive
{
    #[ORM\Column(name: 'log_id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id;

    #[ORM\Column(name: 'log_channel', type: 'string', length: 255, nullable: true)]
    private string $channel;

    #[ORM\Column(name: 'log_level', type: 'string', length: 255, nullable: true)]
    private string $level;

    #[ORM\Column(name: 'log_message', type: 'text', nullable: true)]
    private string $message;

    #[ORM\Column(name: 'log_datetime', type: 'datetime', nullable: false)]
    private \DateTime $datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setDatetime(\DateTime $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getDatetime(): \DateTime
    {
        return $this->datetime;
    }
}
