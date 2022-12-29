<?php

namespace WS\Core\Library\Asset;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\ImageService;

class ImageCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG_RENDITIONS = 'ws.image_renditions';
    public const TAG_CONSUMER = 'ws.image_consumer';

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
            $consumer = $container->findDefinition(strval($taggedService));
            $consumer->addMethodCall('setImageService', [$definition]);
        }
    }
}
