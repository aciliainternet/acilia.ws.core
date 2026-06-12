<?php

namespace WS\Core\Library\Setting;

use WS\Core\Library\Setting\Definition\Section;

interface SettingDefinitionInterface
{
    public const string SETTING_TEXT = 'text';
    public const string SETTING_BOOLEAN = 'boolean';
    public const string SETTING_TEXTAREA = 'textarea';
    public const string SETTING_MULTIPLE = 'multiple';

    /**
     * @return Section[]
     */
    public function getSettingsDefinition(): array;
}
