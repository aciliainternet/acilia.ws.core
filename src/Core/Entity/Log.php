<?php

namespace WS\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ws_log", options={"collate"="utf8_unicode_ci", "charset"="utf8", "engine"="InnoDB"})
 */
class Log
{
    /**
     *
     * @ORM\Column(name="log_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(name="log_channel", type="string", length=255, nullable=true)
     */
    private string $channel;

    /**
     * @ORM\Column(name="log_level", type="string", length=255, nullable=true)
     */
    private string $level;

    /**
     * @ORM\Column(name="log_message", type="text", nullable=true)
     */
    private string $message;

    /**
     * @ORM\Column(name="log_datetime", type="datetime", nullable=false)
     */
    private \DateTime $datetime;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set channel
     *
     * @param  string $channel
     * @return self
     */
    public function setChannel($channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * Set level
     *
     * @param  string $level
     * @return self
     */
    public function setLevel($level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * Set message
     *
     * @param  string $message
     * @return self
     */
    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set datetime
     *
     * @param  \DateTime $datetime
     * @return self
     */
    public function setDatetime(\DateTime $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }
}
