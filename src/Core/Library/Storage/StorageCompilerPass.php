<?php

namespace WS\Core\Library\Storage;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use WS\Core\Service\StorageService;

class StorageCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'storage_service';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(StorageService::class)) {
            return;
        }

        $definition = $container->findDefinition(StorageService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('addDriver', [$taggedService]);
        }
    }
}
