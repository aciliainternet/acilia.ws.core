<?php

namespace WS\Core\Library\DataExport;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Core\Service\DataExportService;

class DataExportCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'ws.data_export';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(DataExportService::class)) {
            return;
        }

        $definition = $container->findDefinition(DataExportService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('addDataExporter', [$taggedService]);
        }
    }
}
