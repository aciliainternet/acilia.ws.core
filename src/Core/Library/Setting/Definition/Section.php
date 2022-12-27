<?php

namespace WS\Core\Library\Setting\Definition;

class Section
{
    protected array $groups;

    public function __construct(
        protected string $code,
        protected string $name,
        protected array $options = []
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->groups = [];
        $this->options = array_merge([
            'description' => '',
            'translation_domain' => 'ws_cms_setting',
            'icon' => 'fa-cog',
            'role' => 'ROLE_WS_SITE',
            'order' => 0
        ], $options);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function addGroup(Group $settingGroupDefinition): Section
    {
        if (isset($this->groups[$settingGroupDefinition->getCode()])) {
            foreach ($settingGroupDefinition->getSettings() as $settingDefinition) {
                $this->groups[$settingGroupDefinition->getCode()]->addSetting($settingDefinition);
            }
        } else {
            $this->groups[$settingGroupDefinition->getCode()] = $settingGroupDefinition;
        }

        return $this;
    }

    /**
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->options['icon'];
    }

    public function getRole(): string
    {
        return $this->options['role'];
    }

    public function getOrder(): int
    {
        return (int) $this->options['order'];
    }

    public function getTranslationDomain(): string
    {
        return $this->options['translation_domain'];
    }

    public function getDescription(): string
    {
        return $this->options['description'];
    }
}
