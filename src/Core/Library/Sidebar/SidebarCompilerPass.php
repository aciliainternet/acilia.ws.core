<?php

namespace WS\Core\Library\Sidebar;

use WS\Core\Service\SidebarService;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class SidebarCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.sidebar_definition';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(SidebarService::class)) {
            return;
        }

        $definition = $container->findDefinition(SidebarService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerSidebarDefinition', [$taggedService]);
        }
    }
}
