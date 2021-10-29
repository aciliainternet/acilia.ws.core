<?php

namespace WS\Core\Service;

use WS\Core\Library\Sidebar\SidebarDefinition;
use WS\Core\Library\Sidebar\SidebarDefinitionInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class SidebarService
{
    protected array $services = [];
    protected ?array $sidebar = null;
    public ParameterBag $assets;

    public function __construct()
    {
        $this->assets = new ParameterBag();
    }

    public function registerSidebarDefinition(SidebarDefinitionInterface $service): void
    {
        $this->services[] = $service;
    }

    public function getSidebarDefinition(string $containerCode, ?string $contentCode = null): ?SidebarDefinition
    {
        // load sidebar definitions
        $this->loadSidebarDefinitions();

        $sidebarContainer = null;
        foreach ((array) $this->sidebar as $container) {
            if ($container->getCode() === $containerCode) {
                $sidebarContainer = $container;
                break;
            }
        }

        if ($sidebarContainer === null) {
            return null;
        }

        if ($contentCode === null) {
            return $sidebarContainer;
        }

        /** @var SidebarDefinition $sidebarContent */
        foreach ($sidebarContainer->getChildren() as $sidebarContent) {
            if ($sidebarContent->getCode() === $contentCode) {
                return $sidebarContent;
            }
        }

        return null;
    }

    public function removeSidebarDefinition(string $containerCode, ?string $contentCode = null): void
    {
        // load sidebar definitions
        $this->loadSidebarDefinitions();

        foreach ((array) $this->sidebar as $keyContainer => $container) {
            if ($container->getCode() === $containerCode) {
                if ($contentCode !== null) {
                    /** @var SidebarDefinition $sidebarContent */
                    foreach ($container->getChildren() as $sidebarContent) {
                        if ($sidebarContent->getCode() === $contentCode) {
                            $container->removeChild($sidebarContent);
                            break;
                        }
                    }
                } else {
                    unset($this->sidebar[$keyContainer]);
                }

                break;
            }
        }
    }

    public function getSidebar(): array
    {
        // load sidebar definitions
        $this->loadSidebarDefinitions();

        $sidebar = [];

        /** @var SidebarDefinition $sidebarDefinition */
        foreach ((array) $this->sidebar as $sidebarDefinition) {
            if (isset($sidebar[$sidebarDefinition->getCode()])) {
                foreach ($sidebarDefinition->getChildren() as $menu) {
                    $sidebar[$sidebarDefinition->getCode()]->addChild($menu);
                }
            } else {
                $sidebar[$sidebarDefinition->getCode()] = $sidebarDefinition;
            }
        }

        // order content menus
        foreach ($sidebar as $menu) {
            if ($menu->isContainer()) {
                $sidebarContents = $menu->getChildren();
                usort($sidebarContents, function (SidebarDefinition $menu1, SidebarDefinition $menu2) {
                    return strcmp((string) $menu1->getOrder(), (string) $menu2->getOrder());
                });
                $menu->setChildren($sidebarContents);
            }
        }

        // order containers menu
        usort($sidebar, function (SidebarDefinition $menu1, SidebarDefinition $menu2) {
            return strcmp((string) $menu1->getOrder(), (string) $menu2->getOrder());
        });

        return $sidebar;
    }

    protected function loadSidebarDefinitions(): void
    {
        if ($this->sidebar === null) {
            $this->sidebar = [];
            foreach ($this->services as $service) {
                foreach ($service->getSidebarDefinition() as $definition) {
                    if ($definition instanceof SidebarDefinition) {
                        $this->sidebar[$definition->getCode()] = $definition;
                    }
                }
            }
        }
    }
}
