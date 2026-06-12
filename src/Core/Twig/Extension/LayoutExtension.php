<?php

namespace WS\Core\Twig\Extension;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Twig\Attribute\AsTwigFunction;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Service\SidebarService;

class LayoutExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SidebarService $sidebarService,
        private readonly ?AuthorizationCheckerInterface $securityChecker = null
    ) {
    }

    #[AsTwigFunction(name: 'ws_cms_sidebar_get')]
    public function getSidebar(): array
    {
        return $this->sidebarService->getSidebar();
    }

    #[AsTwigFunction(name: 'ws_cms_sidebar_is_granted')]
    public function sidebarIsGranted(array $roles): bool
    {
        if (null === $this->securityChecker) {
            return false;
        }

        try {
            array_walk($roles, function (&$value): void {
                $value = sprintf('is_granted(\'%s\')', $value);
            });

            return $this->securityChecker->isGranted(new Expression(implode(' or ', $roles)));
        } catch (AuthenticationCredentialsNotFoundException) {
            return false;
        }
    }

    #[AsTwigFunction(name: 'ws_cms_sidebar_has_asset')]
    public function sidebarHasAsset(string $key): bool
    {
        return null !== $this->sidebarService->getAsset($key);
    }

    #[AsTwigFunction(name: 'ws_cms_sidebar_get_asset')]
    public function sidebarGetAsset(string $key): ?string
    {
        /** @var ?string */
        return $this->sidebarService->getAsset($key);
    }

    #[AsTwigFunction(name: 'ws_cms_in_route', isSafe: ['html'])]
    public function checkIfInRoute(
        array $routePrefix,
        string $class = 'active',
        ?bool $condition = null,
        array $routeParameters = []
    ): string {
        if ($this->requestStack->getMainRequest() instanceof Request) {
            foreach ($routePrefix as $route) {
                /** @var string $routeName */
                $routeName = $this->requestStack->getMainRequest()->attributes->get('_route');
                if (str_starts_with(strval($routeName), (string) $route)) {
                    if ($condition === false) {
                        return '';
                    }

                    if (!empty($routeParameters)) {
                        $routeParams = array_merge(
                            (array) $this->requestStack->getMainRequest()->attributes->get('_route_params'),
                            (array) $this->requestStack->getMainRequest()->query->all()
                        );

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
