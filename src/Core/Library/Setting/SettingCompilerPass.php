<?php

namespace WS\Core\Library\Setting;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\SettingService;

class SettingCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.setting_definition';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(SettingService::class)) {
            return;
        }

        $definition = $container->findDefinition(SettingService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerSettingDefinition', [$taggedService]);
        }
    }
}
