<?php

namespace WS\Core\Service;

use WS\Core\Library\Sidebar\SidebarDefinition;
use WS\Core\Library\Sidebar\SidebarDefinitionInterface;

class SidebarDefinitionService implements SidebarDefinitionInterface
{
    protected SettingService $settingService;
    protected SidebarService $sidebarService;

    public function __construct(SettingService $settingService, SidebarService $sidebarService)
    {
        $this->settingService = $settingService;
        $this->sidebarService = $sidebarService;

        // Set Default Logo
        $this->sidebarService->assets->set('logo', '/cms/images/logo-acilia.svg');
    }

    public function getSidebarDefinition(): array
    {
        // dashboard menu
        $dashboardNode = new SidebarDefinition(
            'dashboard',
            'dashboard.menu',
            [
                'route_name' => 'ws_dashboard',
            ],
            [
                'roles' => [],
                'translation_domain' => 'ws_cms',
                'icon' => 'fa-tachometer-alt-fast',
                'collapsed_routes' => ['ws_dashboard'],
                'order' => 1,
            ]
        );

        // translation menu
        $translationsNode = new SidebarDefinition(
            'translations',
            'menu',
            [
                'route_name' => 'ws_translation_index',
            ],
            [
                'roles' => ['ROLE_WS_CORE_TRANSLATION'],
                'translation_domain' => 'ws_cms_translation',
                'icon' => 'fa-language',
                'collapsed_routes' => ['ws_translation_'],
                'order' => 5,
            ]
        );

        // settings menu
        $settingsNode = new SidebarDefinition(
            'settings',
            'menu',
            null,
            [
                'roles' => ['ROLE_WS_CORE_SETTINGS'],
                'container' => SidebarDefinition::SIDEBAR_CONTAINER,
                'translation_domain' => 'ws_cms_setting',
                'icon' => 'fa-cogs',
                'collapsed_routes' => ['ws_setting_'],
                'order' => 6,
            ]
        );

        // settings section menu
        foreach ($this->settingService->getSections() as $settingSectionDefinition) {

            // sectionCode menu
            $settingsNode->addChild(new SidebarDefinition(
                $settingSectionDefinition->getCode(),
                $settingSectionDefinition->getName(),
                [
                    'route_name' => 'ws_setting_index',
                    'route_options' => ['section' => $settingSectionDefinition->getCode()],
                ],
                [
                    'roles' => [$settingSectionDefinition->getRole()],
                    'translation_domain' => $settingSectionDefinition->getTranslationDomain(),
                    'icon' => $settingSectionDefinition->getIcon(),
                    'collapsed_routes' => ['ws_setting_'],
                    'order' => $settingSectionDefinition->getOrder(),
                ]
            ));
        }

        // administrator menu
        $administratorsNode = new SidebarDefinition(
            'administrators',
            'menu',
            [
                'route_name' => 'ws_administrator_index',
            ],
            [
                'roles' => ['ROLE_WS_CORE_ADMINISTRATOR'],
                'translation_domain' => 'ws_cms_administrator',
                'icon' => 'fa-user-shield',
                'collapsed_routes' => ['ws_administrator_'],
                'order' => 7,
            ]
        );

        return [
            $dashboardNode,
            $translationsNode,
            $settingsNode,
            $administratorsNode,
        ];
    }
}
