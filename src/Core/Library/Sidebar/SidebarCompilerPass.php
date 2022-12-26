<?php

namespace WS\Core\Library\Sidebar;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\SidebarService;

class SidebarCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.sidebar_definition';

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
