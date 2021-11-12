<?php

namespace WS\Core\Library\Alert;

use WS\Core\Service\AlertService;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AlertCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.alert_gatherer';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(AlertService::class)) {
            return;
        }

        $definition = $container->findDefinition(AlertService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('addGatherer', [$taggedService]);
        }
    }
}
