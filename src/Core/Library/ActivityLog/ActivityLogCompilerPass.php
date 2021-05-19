<?php

namespace WS\Core\Library\ActivityLog;

use WS\Core\Service\ActivityLogService;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ActivityLogCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.activity_log';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ActivityLogService::class)) {
            return;
        }

        $definition = $container->findDefinition(ActivityLogService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerService', [$taggedService]);
        }
    }
}
