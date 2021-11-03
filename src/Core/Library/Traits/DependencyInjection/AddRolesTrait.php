<?php

namespace WS\Core\Library\Traits\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait AddRolesTrait
{
    public function addRoles(ContainerBuilder $container, array $roles): void
    {
        $roles = array_merge_recursive(
            (array) $container->getParameter('security.role_hierarchy.roles'),
            $roles
        );

        $container->setParameter('security.role_hierarchy.roles', $roles);
    }
}
