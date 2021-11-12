<?php

namespace WS\Core\Library\Dashboard;

use WS\Core\Service\DashboardService;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class DashboardWidgetCompilerPass implements CompilerPassInterface
{
    const TAG = 'ws.dashboard_widget';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(DashboardService::class)) {
            return;
        }

        $definition = $container->findDefinition(DashboardService::class);

        foreach ($container->findTaggedServiceIds(self::TAG, true) as $serviceId => $attributes) {
            if (isset($attributes[0]) && isset($attributes[0]['disabled']) && $attributes[0]['disabled']) {
                continue;
            } else {
                $definition->addMethodCall('addWidget', [new Reference($serviceId)]);
            }
        }
    }
}
