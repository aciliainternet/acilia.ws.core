<?php

namespace WS\Core\Library\Asset;

class RenditionDefinition
{
    const METHOD_THUMB = 'thumb';
    const METHOD_CROP = 'crop';

    protected string $class;
    protected string $field;
    protected string $name;
    protected ?int $width;
    protected ?int $height;
    protected string $method;
    protected ?array $subRenditions;
    protected int $quality;

    public function __construct(
        string $class,
        string $field,
        string $name,
        ?int $width,
        ?int $height,
        string $method,
        ?array $subRenditions = null,
        int $quality = 90
    ) {
        $this->class = $class;
        $this->field = $field;
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->method = $method;
        $this->subRenditions = $subRenditions;
        $this->quality = $quality;
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
