<?php

namespace WS\Core\Library\Navbar;

class NavbarDefinition
{
    protected string $code;
    protected string $label;
    protected ?array $route;
    protected array $options;

    public function __construct(
        string $code,
        string $label,
        array $route = null,
        array $options = []
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->route = $route;

        $this->options = array_merge([
            'translation_domain' => 'cms',
            'icon' => 'fa-angle-right',
            'order' => 999,
            'roles' => [],
            'divider' => false,
            'template' => ''

        ], $options);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRouteName(): string
    {
        if (isset($this->route['route_name'])) {
            return $this->route['route_name'];
        }

        return '#';
    }

    public function getRouteOptions(): array
    {
        if (isset($this->route['route_options'])) {
            return $this->route['route_options'];
        }

        return [];
    }

    public function getRoles(): array
    {
        return $this->options['roles'];
    }

    public function setRoles(array $roles): NavbarDefinition
    {
        $this->options['roles'] = $roles;

        return $this;
    }

    public function addRole(string $role): NavbarDefinition
    {
        $this->options['roles'][] = $role;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->options['icon'];
    }

    public function setOrder(int $order): NavbarDefinition
    {
        $this->options['order'] = $order;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->options['order'];
    }

    public function getTranslationDomain(): string
    {
        return $this->options['translation_domain'];
    }

    public function hasDivider(): bool
    {
        return $this->options['divider'];
    }

    public function getTemplate(): string
    {
        return $this->options['template'];
    }
}
