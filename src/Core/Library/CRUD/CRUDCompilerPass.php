<?php

namespace WS\Core\Library\CRUD;

use WS\Core\Service\FileService;
use WS\Core\Service\ImageService;
use WS\Core\Service\DataExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;

class CRUDCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.crud_controller';

    public function process(ContainerBuilder $container): void
    {
        // Get Translation Service Definition
        $translatorDefinition = null;
        if ($container->has(TranslatorInterface::class)) {
            $translatorDefinition = $container->findDefinition(TranslatorInterface::class);
        }

        // Get Image Service Definition
        $imageServiceDefinition = null;
        if ($container->has(ImageService::class)) {
            $imageServiceDefinition = $container->findDefinition(ImageService::class);
        }

        // Get File Service Definition
        $fileServiceDefinition = null;
        if ($container->has(FileService::class)) {
            $fileServiceDefinition = $container->findDefinition(FileService::class);
        }

        // Get DataExport Service Definition
        $dataExportServiceDefinition = null;
        if ($container->has(DataExportService::class)) {
            $dataExportServiceDefinition = $container->findDefinition(DataExportService::class);
        }

        // Get Doctrine Service Definition
        $entityManagerDefinition = null;
        if ($container->has(EntityManagerInterface::class)) {
            $entityManagerDefinition = $container->findDefinition(EntityManagerInterface::class);
        }

        // Get all tagged CRUD Controllers
        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            // Get CRUD Controller Definition
            $definition = $container->findDefinition($taggedService);

            // Link Translation Service
            if ($translatorDefinition) {
                $definition->addMethodCall('setTranslator', [$translatorDefinition]);
            }

            // Link Image Service
            if ($imageServiceDefinition) {
                $definition->addMethodCall('setImageService', [$imageServiceDefinition]);
            }

            // Link File Service
            if ($fileServiceDefinition) {
                $definition->addMethodCall('setFileService', [$fileServiceDefinition]);
            }

            // Link DataExport Service
            if ($dataExportServiceDefinition) {
                $definition->addMethodCall('setDataExportService', [$dataExportServiceDefinition]);
            }

            // Link Doctrine Service
            if ($entityManagerDefinition) {
                $definition->addMethodCall('setDoctrine', [$entityManagerDefinition]);
            }
        }
    }
}
