<?php

namespace WS\Core\Service;

use Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use WS\Core\Library\Sidebar\SidebarDefinition;
use WS\Core\Library\Sidebar\SidebarDefinitionInterface;

class SidebarService
{
    private ?array $sidebar = null;
    private ?array $assets = null;

    public function __construct(
        #[TaggedLocator(SidebarDefinitionInterface::class, defaultPriorityMethod: 'getPriority')]
        private ServiceLocator $services,
    ) {
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
                usort($sidebarContents, fn (SidebarDefinition $menu1, SidebarDefinition $menu2) => strcmp((string) $menu1->getOrder(), (string) $menu2->getOrder()));
                $menu->setChildren($sidebarContents);
            }
        }

        // order containers menu
        usort($sidebar, fn (SidebarDefinition $menu1, SidebarDefinition $menu2) => strcmp((string) $menu1->getOrder(), (string) $menu2->getOrder()));

        return $sidebar;
    }

    public function getAsset(string $key): mixed
    {
        // load sidebar assets
        $this->loadSidebarAssets();

        return isset($this->assets[$key]) ? $this->assets[$key] : null;
    }

    private function loadSidebarDefinitions(): void
    {
        if ($this->sidebar === null) {
            $this->sidebar = [];
            foreach ($this->services->getProvidedServices() as $service) {
                foreach ($this->services->get($service)->getSidebarDefinition() as $definition) {
                    if ($definition instanceof SidebarDefinition) {
                        $this->sidebar[$definition->getCode()] = $definition;
                    }
                }
            }
        }
    }

    private function loadSidebarAssets(): void
    {
        if ($this->assets === null) {
            $this->assets = [];
            foreach ($this->services->getProvidedServices() as $service) {
                foreach ($this->services->get($service)->getSidebarAssets() as $asset) {
                    $this->assets[$asset['key']] = $asset['value'];
                }
            }
        }
    }
}
