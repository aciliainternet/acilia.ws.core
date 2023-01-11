<?php

namespace WS\Core\Library\Traits\CRUD;

trait RoleCalculatorTrait
{
    public function calculateRole(string $class, ?string $action = null): string
    {
        $classPath = explode('\\', $class);

        if ($classPath[0] === 'WS') {
            $role = sprintf('ROLE_WS_%s_%s', $classPath[1], $classPath[3]);
            if ($action !== null) {
                $role = sprintf('%s_%s', $role, $action);
            }
        } elseif ($classPath[0] === 'App') {
            $classWords = preg_split('/(?=[A-Z])/', $classPath[2], -1, PREG_SPLIT_NO_EMPTY);
            if (false === $classWords) {
                throw new \Exception(sprintf('Unable to calculate Access Role for class: %s', $class));
            }

            $classRole = implode('_', $classWords);
            $role = sprintf('ROLE_APP_%s', $classRole);
            if ($action !== null) {
                $role = sprintf('%s_%s', $role, $action);
            }
        } else {
            throw new \Exception(sprintf('Unable to calculate Access Role for class: %s', $class));
        }

        return strtoupper($role);
    }
}
