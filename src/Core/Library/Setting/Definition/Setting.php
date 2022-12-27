<?php

namespace WS\Core\Library\Setting\Definition;

class Setting
{
    protected ?string $value;

    public function __construct(
        protected string $code,
        protected string $name,
        protected string $type,
        protected array $options = []
    ) {
        $this->value = null;

        $this->options = array_merge([
            'description' => '',
            'placeholder' => '',
            'translation_domain' => 'ws_cms_setting',
            'required' => false,
            'default' => null,
            'options' => []
        ], $options);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->options['required'];
    }

    public function getTranslationDomain(): string
    {
        return $this->options['translation_domain'];
    }

    public function getDescription(): string
    {
        return $this->options['description'];
    }

    public function getValue(): ?string
    {
        if ($this->value === null) {
            return $this->options['default'];
        }

        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getPlaceholder(): string
    {
        return $this->options['placeholder'];
    }

    public function getDefault(): ?string
    {
        return $this->options['default'];
    }

    public function getOptions(): array
    {
        return $this->options['options'];
    }
}
