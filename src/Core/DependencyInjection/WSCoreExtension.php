<?php

namespace WS\Core\DependencyInjection;

use WS\Core\Entity\Administrator;
use WS\Core\Library\Asset\ImageConsumerInterface;
use WS\Core\Library\DataExport\DataExportCompilerPass;
use WS\Core\Library\DataExport\DataExportProviderInterface;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Library\CRUD\CRUDCompilerPass;
use WS\Core\Library\FactoryCollector\FactoryCollectorCompilerPass;
use WS\Core\Library\FactoryCollector\FactoryCollectorInterface;
use WS\Core\Library\Navbar\NavbarCompilerPass;
use WS\Core\Library\Navbar\NavbarDefinitionInterface;
use WS\Core\Library\Navigation\NavigationCompilerPass;
use WS\Core\Library\Navigation\NavigationProviderInterface;
use WS\Core\Service\ActivityLogService;
use WS\Core\Library\ActivityLog\ActivityLogCompilerPass;
use WS\Core\Library\ActivityLog\ActivityLogInterface;
use WS\Core\Library\Alert\AlertCompilerPass;
use WS\Core\Library\Alert\AlertGathererInterface;
use WS\Core\Library\Asset\ImageCompilerPass;
use WS\Core\Library\CRUD\RoleCalculatorTrait;
use WS\Core\Library\CRUD\RoleLoaderTrait;
use WS\Core\Library\Dashboard\DashboardWidgetCompilerPass;
use WS\Core\Library\Setting\SettingCompilerPass;
use WS\Core\Library\Sidebar\SidebarCompilerPass;
use WS\Core\Library\Sidebar\SidebarDefinitionInterface;
use WS\Core\Library\Traits\DependencyInjection\AddRolesTrait;
use WS\Core\Library\Asset\ImageRenditionInterface;
use WS\Core\Library\Dashboard\DashboardWidgetInterface;
use WS\Core\Library\DBLogger\DBLoggerInterface;
use WS\Core\Library\Setting\SettingDefinitionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use WS\Core\Service\TranslationService;

class WSCoreExtension extends Extension implements PrependExtensionInterface
{
    use RoleCalculatorTrait;
    use RoleLoaderTrait;
    use AddRolesTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('router.yaml');

        $masterRole = 'ROLE_WS_CORE';
        $actions = ['view', 'create', 'edit', 'delete'];
        $entities = [
            Administrator::class,
        ];

        $this->loadRoles($container, $masterRole, $entities, $actions);

        $this->addRoles($container, [
            'ROLE_WS_CORE' => [
                'ROLE_WS_CORE_ADMINISTRATOR',
                'ROLE_WS_CORE_TRANSLATION',
                'ROLE_WS_CORE_ACTIVITY_LOG',
                'ROLE_WS_CORE_SETTINGS'
            ]
        ]);

        // Tag with DB Channel to all DBLoggerInterface services
        $container->registerForAutoconfiguration(DBLoggerInterface::class)->addTag('monolog.logger', ['channel' => 'db']);

        // Tag Dashboard Widgets
        $container->registerForAutoconfiguration(DashboardWidgetInterface::class)->addTag(DashboardWidgetCompilerPass::TAG);

        // Tag Setting Providers
        $container->registerForAutoconfiguration(SettingDefinitionInterface::class)->addTag(SettingCompilerPass::TAG);

        // Tag Image Rendition Definitions
        $container->registerForAutoconfiguration(ImageRenditionInterface::class)->addTag(ImageCompilerPass::TAG_RENDITIONS);

        // Tag Image Consumers
        $container->registerForAutoconfiguration(ImageConsumerInterface::class)->addTag(ImageCompilerPass::TAG_CONSUMER);

        // Tag Factory Objects
        $container->registerForAutoconfiguration(FactoryCollectorInterface::class)->addTag(FactoryCollectorCompilerPass::TAG);

        // Tag Activity Logs
        $container->registerForAutoconfiguration(ActivityLogInterface::class)->addTag(ActivityLogCompilerPass::TAG);

        // Tag Alert Gatherers
        $container->registerForAutoconfiguration(AlertGathererInterface::class)->addTag(AlertCompilerPass::TAG);

        // Tag Sidebars Definitions
        $container->registerForAutoconfiguration(SidebarDefinitionInterface::class)->addTag(SidebarCompilerPass::TAG);

        // Tag Navbars Definitions
        $container->registerForAutoconfiguration(NavbarDefinitionInterface::class)->addTag(NavbarCompilerPass::TAG);

        // Tag Data Exporters
        $container->registerForAutoconfiguration(DataExportProviderInterface::class)->addTag(DataExportCompilerPass::TAG);

        // Tag CRUD Controllers
        $container->registerForAutoconfiguration(AbstractController::class)->addTag(CRUDCompilerPass::TAG);

        // Tag Navigation Providers
        $container->registerForAutoconfiguration(NavigationProviderInterface::class)->addTag(NavigationCompilerPass::TAG);

        // Configure services
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Configure Activity Log
        $activityLogService = $container->getDefinition(ActivityLogService::class);
        $activityLogService->setArgument(0, $config['activity_log']);

        // Configure Translations
        $translationsService = $container->getDefinition(TranslationService::class);
        $translationsService->setArgument(0, $config['translations']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                // Register DBLogger on Monolog
                case 'monolog':
                    $container->prependExtensionConfig($name, [
                        'channels' => ['db'],
                        'handlers' => [
                            'db' => [
                                'channels' => ['db'],
                                'type' => 'service',
                                'id' => 'WS\Core\Service\DBLoggerService'
                            ]
                        ]
                    ]);
                    break;
            }
        }
    }
}
