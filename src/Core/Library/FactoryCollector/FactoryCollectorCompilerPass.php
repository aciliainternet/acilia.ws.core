<?php

namespace WS\Core\Library\FactoryCollector;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\FactoryCollectorService;

class FactoryCollectorCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.factory_collector';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FactoryCollectorService::class)) {
            return;
        }

        $definition = $container->findDefinition(FactoryCollectorService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerService', [$taggedService]);
        }
    }
}
