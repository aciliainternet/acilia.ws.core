<?php

namespace WS\Core\Library\Setting;

use WS\Core\Library\Setting\Definition\Section;

interface SettingDefinitionInterface
{
    public const SETTING_TEXT = 'text';
    public const SETTING_BOOLEAN = 'boolean';
    public const SETTING_TEXTAREA = 'textarea';
    public const SETTING_MULTIPLE = 'multiple';

    /**
     * @return Section[]
     */
    public function getSettingsDefinition(): array;
}
