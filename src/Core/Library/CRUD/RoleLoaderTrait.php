<?php

namespace WS\Core\Library\CRUD;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait RoleLoaderTrait
{
    public function loadRoles(ContainerBuilder $container, string $masterRole, array $entities, array $actions): void
    {
        $roles = [];
        $roles[$masterRole] = [];
        foreach ($entities as $entity) {
            $parentRole = $this->calculateRole($entity);
            $roles[$masterRole][] = $parentRole;
            $roles[$parentRole] = [];
            foreach ($actions as $action) {
                $roles[$parentRole][] = $this->calculateRole($entity, $action);
            }
        }

        $this->addRoles($container, $roles);
    }
}
