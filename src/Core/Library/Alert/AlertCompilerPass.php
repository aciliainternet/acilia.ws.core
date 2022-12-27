<?php

namespace WS\Core\Library\Alert;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\AlertService;

class AlertCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.alert_gatherer';

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
