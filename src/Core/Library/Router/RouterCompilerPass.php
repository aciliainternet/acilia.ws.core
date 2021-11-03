<?php

namespace WS\Core\Library\Router;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RouterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->setAlias('router', Router::class)->setPublic(true);
    }
}
