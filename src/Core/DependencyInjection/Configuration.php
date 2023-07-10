<?php

namespace WS\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ws_core');

        $root = $treeBuilder->getRootNode();
        $root
            ->children()
                ->booleanNode('activity_log')
                    ->info('Disables or Enables the Activity Log service.')
                    ->defaultTrue()
                ->end() // activity_log

                ->arrayNode('translations')
                    ->info('Allows to configure site translations.')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('sources')
                            ->prototype('scalar')
                            ->end()
                            ->defaultValue(['ws'])
                        ->end()
                    ->end()
                ->end() // translation

                ->arrayNode('preview')
                    ->info('Disables or Enables the frontend preview service.')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                        ->defaultFalse()
                        ->end()
                        ->scalarNode('path')
                        ->defaultValue('preview')
                        ->end()
                        ->scalarNode('ttl')
                        ->defaultValue(2592000)
                        ->end()
                        ->arrayNode('locales')
                            ->prototype('scalar')
                            ->end()
                            ->defaultValue(['en'])
                        ->end()
                    ->end()
                ->end() // preview
            ->end();

        return $treeBuilder;
    }
}
