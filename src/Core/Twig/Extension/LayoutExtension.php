<?php

namespace WS\Core\Twig\Extension;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use WS\Core\Service\SidebarService;
use WS\Core\Service\NavbarService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private SidebarService $sidebarService;
    private NavbarService $navbarService;
    private ?AuthorizationCheckerInterface $securityChecker;

    public function __construct(
        RequestStack $requestStack,
        SidebarService $sidebarService,
        NavbarService $navbarService,
        ?AuthorizationCheckerInterface $securityChecker = null
    ) {
        $this->requestStack = $requestStack;
        $this->sidebarService = $sidebarService;
        $this->navbarService = $navbarService;
        $this->securityChecker = $securityChecker;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ws_cms_sidebar_get', [$this, 'getSidebar']),
            new TwigFunction('ws_cms_sidebar_is_granted', [$this, 'sidebarIsGranted']),
            new TwigFunction('ws_cms_sidebar_has_asset', [$this, 'sidebarHasAsset']),
            new TwigFunction('ws_cms_sidebar_get_asset', [$this, 'sidebarGetAsset']),
            new TwigFunction('ws_cms_navbar_get', [$this, 'getNavbar']),
            new TwigFunction('ws_cms_in_route', [$this, 'checkIfInRoute'], ['is_safe' => ['html']]),
        ];
    }

    public function getSidebar(): array
    {
        return $this->sidebarService->getSidebar();
    }

    public function sidebarIsGranted(array $roles): bool
    {
        if (null === $this->securityChecker) {
            return false;
        }

        try {
            array_walk($roles, function (&$value) {
                $value = sprintf('is_granted(\'%s\')', $value);
            });

            return $this->securityChecker->isGranted(new Expression(implode(' or ', $roles)));
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    public function sidebarHasAsset(string $key): bool
    {
        return $this->sidebarService->assets->has($key);
    }

    public function sidebarGetAsset(string $key): ?string
    {
        /** @var ?string */
        return $this->sidebarService->assets->get($key);
    }

    public function getNavbar(): array
    {
        return $this->navbarService->getNavbar();
    }

    public function checkIfInRoute(
        array $routePrefix,
        string $class = 'active',
        bool $condition = null,
        array $routeParameters = []
    ): string {
        if ($this->requestStack->getMainRequest() instanceof Request) {
            foreach ($routePrefix as $route) {
                if (strpos(strval($this->requestStack->getMainRequest()->get('_route')), $route) === 0) {
                    if ($condition === false) {
                        return '';
                    }

                    if ($routeParameters) {
                        $routeParams = (array) $this->requestStack->getMainRequest()->get('_route_params');

                        foreach ($routeParameters as $k => $v) {
                            if (!isset($routeParams[$k]) || $routeParams[$k] != $v) {
                                return '';
                            }
                        }
                    }

                    return $class;
                }
            }
        }

        return '';
    }
}
