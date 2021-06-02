<?php

namespace WS\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ws_core');

        $root = $treeBuilder->getRootNode();
        $root
            ->children()
                ->booleanNode('activity_log')
                    ->defaultTrue()
                    ->info('Disables or Enables the Activity Log service.')
                ->end() // activity_log
                ->arrayNode('translations')
                    ->addDefaultsIfNotSet()
                    ->info('Allows to configure site translations.')
                    ->children()
                        ->arrayNode('sources')
                            ->prototype('scalar')
                            ->end()
                            ->defaultValue(['ws'])
                        ->end()
                    ->end()
                ->end() // translation
            ->end()
        ;

        return $treeBuilder;
    }
}
