<?php

namespace WS\Core\Service\Entity;

use WS\Core\Library\ActivityLog\ActivityLogInterface;
use WS\Core\Library\ActivityLog\ActivityLogTrait;
use WS\Core\Library\CRUD\AbstractService;

class AdministratorService extends AbstractService implements ActivityLogInterface
{
    use ActivityLogTrait;

    protected array $roles = [];

    public function getSortFields(): array
    {
        return ['name'];
    }

    public function getFilterFields(): array
    {
        return ['name', 'email'];
    }

    public function getListFields(): array
    {
        return [
            ['name' => 'name'],
            ['name' => 'email'],
            ['name' => 'profile', 'filter' => 'ws_cms_administrator_profile'],
            ['name' => 'createdAt', 'width' => 200, 'isDate' => true],
        ];
    }

    public function addRoles(array $roles): void
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole(string $role): void
    {
        $this->roles[] = $role;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function getFormProfiles(): array
    {
        $profiles = [];

        foreach ($this->roles as $role) {
            $profile = sprintf('administrator_role.%s', str_replace('ROLE_', '', $role));
            $profiles[strtolower($profile)] = $role;
        }

        return $profiles;
    }

    public function getProfileLabel(string $profile): string
    {
        return sprintf('administrator_role.%s', strtolower(str_replace('ROLE_', '', $profile)));
    }
}
