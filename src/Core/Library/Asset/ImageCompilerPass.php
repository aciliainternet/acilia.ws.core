<?php

namespace WS\Core\Library\Asset;

use WS\Core\Service\ImageService;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ImageCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG_RENDITIONS = 'ws.image_renditions';
    const TAG_CONSUMER = 'ws.image_consumer';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ImageService::class)) {
            return;
        }

        $definition = $container->findDefinition(ImageService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG_RENDITIONS, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerRenditions', [$taggedService]);
        }

        $taggedServices = $this->findAndSortTaggedServices(self::TAG_CONSUMER, $container);
        foreach ($taggedServices as $taggedService) {
            $consumer = $container->findDefinition($taggedService);
            $consumer->addMethodCall('setImageService', [$definition]);
        }
    }
}
