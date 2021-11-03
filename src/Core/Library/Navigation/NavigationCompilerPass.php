<?php

namespace WS\Core\Library\Navigation;

use WS\Core\Library\Router\Router;
use WS\Core\Service\NavigationService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NavigationCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.navigation_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(NavigationService::class)) {
            return;
        }

        $definition = $container->findDefinition(NavigationService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('addProvider', [$taggedService]);
        }

        $routerDefinition = $container->findDefinition(Router::class);
        $routerDefinition->addMethodCall('setNavigationService', [$definition]);
    }
}
