<?php

namespace WS\Core\Library\Setting\Definition;

class Group
{
    protected string $code;
    protected string $name;
    protected array $settings;
    protected array $options;

    public function __construct(string $code, string $name, array $options = [])
    {
        $this->code = $code;
        $this->name = $name;
        $this->settings = [];
        $this->options = array_merge([
            'description' => '',
            'translation_domain' => 'ws_cms_setting',
            'order'  => 0
        ], $options);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function addSetting(Setting $settingDefinition): Group
    {
        $this->settings[$settingDefinition->getCode()] = $settingDefinition;

        return $this;
    }

    /**
     * @return Setting[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getDescription(): string
    {
        return $this->options['description'];
    }

    public function getTranslationDomain(): string
    {
        return $this->options['translation_domain'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrder(): int
    {
        return (int) $this->options['order'];
    }
}
