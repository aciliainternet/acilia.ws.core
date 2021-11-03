<?php

namespace WS\Core\Library\Navbar;

use WS\Core\Service\NavbarService;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class NavbarCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.navbar_definition';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(NavbarService::class)) {
            return;
        }

        $definition = $container->findDefinition(NavbarService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerNavbarDefinition', [$taggedService]);
        }
    }
}
