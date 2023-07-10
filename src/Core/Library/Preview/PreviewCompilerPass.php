<?php

namespace WS\Core\Library\Preview;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\PreviewService;

class PreviewCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.preview';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(PreviewService::class)) {
            return;
        }

        $definition = $container->findDefinition(PreviewService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerService', [$taggedService]);
        }
    }
}
