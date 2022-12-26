<?php

namespace WS\Core\Library\DataCollector;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DataCollectorCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(BuildCollector::class)) {
            return;
        }

        $definition = $container->findDefinition(BuildCollector::class);

        $taggedServices = $this->findAndSortTaggedServices('ws.component', $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('addComponent', [$taggedService]);
        }
    }
}
