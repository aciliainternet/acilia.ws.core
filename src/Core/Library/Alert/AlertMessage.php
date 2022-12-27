<?php

namespace WS\Core\Library\Alert;

class AlertMessage
{
    public function __construct(
        protected string $message,
        protected ?string $iconClass = null,
        protected ?string $routeName = null,
        protected array $routeOptions = []
    ) {
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
