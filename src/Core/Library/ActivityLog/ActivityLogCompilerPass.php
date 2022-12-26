<?php

namespace WS\Core\Library\ActivityLog;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\ActivityLogService;

class ActivityLogCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.activity_log';

    public function process(ContainerBuilder $container): void
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
