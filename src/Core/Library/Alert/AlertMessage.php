<?php

namespace WS\Core\Library\Alert;

class AlertMessage
{
    protected string $message;
    protected ?string $iconClass;
    protected ?string $routeName;
    protected array $routeOptions;

    public function __construct(
        string $message,
        ?string $iconClass = null,
        ?string $routeName = null,
        array $routeOptions = []
    ) {
        $this->message = $message;
        $this->iconClass = $iconClass;
        $this->routeName = $routeName;
        $this->routeOptions = $routeOptions;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getIconClass(): ?string
    {
        return $this->iconClass;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function getRouteOptions(): array
    {
        return $this->routeOptions;
    }
}
