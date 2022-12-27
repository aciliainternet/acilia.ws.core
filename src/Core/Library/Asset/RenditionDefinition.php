<?php

namespace WS\Core\Library\Asset;

class RenditionDefinition
{
    public const METHOD_THUMB = 'thumb';
    public const METHOD_CROP = 'crop';

    public function __construct(
        protected string $class,
        protected string $field,
        protected string $name,
        protected ?int $width,
        protected ?int $height,
        protected string $method,
        protected ?array $subRenditions = null,
        protected int $quality = 90
    ) {
    }

    public function getAspectRatio(): ?string
    {
        if (is_numeric($this->width) && is_numeric($this->height)) {
            $gcd = gmp_gcd((int) $this->width, (int) $this->height);
            $max = \intval(gmp_strval($gcd, 10));

            return sprintf('%d:%d', (int) $this->width / $max, (int) $this->height / $max);
        }

        return null;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getSubRenditions(): array
    {
        $masterRendition = sprintf('%dx%d', $this->getWidth(), $this->getHeight());
        if (!is_array($this->subRenditions)) {
            $this->subRenditions = [$masterRendition];
        } elseif (!in_array($masterRendition, $this->subRenditions)) {
            $this->subRenditions[] = $masterRendition;
        }

        return $this->subRenditions;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }
}
