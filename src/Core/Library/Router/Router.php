<?php

namespace WS\Core\Library\Router;

use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class Router implements WarmableInterface, ServiceSubscriberInterface, RouterInterface
{
    private RoutingLoader $loader;

    public function __construct(
        private BaseRouter $router,
        private string $defaultLocale
    ) {
    }

    public function setLoader(RoutingLoader $loader): void
    {
        $this->loader = $loader;
    }

    public function getLoader(): RoutingLoader
    {
        return $this->loader;
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->router->getRouteCollection();
    }

    public function warmUp(string $cacheDir): array
    {
        return $this->router->warmUp($cacheDir);
    }

    public static function getSubscribedServices(): array
    {
        return [
            'routing.loader' => LoaderInterface::class,
        ];
    }

    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }

    public function matchRequest(Request $request): array
    {
        return $this->router->matchRequest($request);
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $loader = $this->getLoader();

        // define parameters based on loader needs
        $parameters = array_merge(
            $parameters,
            $loader->getParameters($this->getContext())
        );

        try {
            // let symfony generate the route
            return $this->router->generate($name, $parameters, $referenceType);
        } catch (RouteNotFoundException $e) {
        }

        // let ws generate the route
        $wsName = sprintf('%s/%s', $name, $this->getLocale($parameters));
        try {
            return $this->router->generate($wsName, $parameters, $referenceType);
        } catch (RouteNotFoundException $e) {
        }

        throw new RouteNotFoundException(sprintf('Route "%s" not found', $name));
    }

    public function match(string $pathinfo): array
    {
        return $this->router->match($pathinfo);
    }

    protected function getLocale(?array $parameters = []): string
    {
        if (isset($parameters['_locale'])) {
            return $parameters['_locale'];
        }

        $currentLocale = $this->getContext()->getParameter('_locale');
        if (null !== $currentLocale) {
            return strval($currentLocale);
        }

        return $this->defaultLocale;
    }

    public function getContextParams(string $name, array $params): array
    {
        $contextParams = [];
        if (empty($params)) {
            return $contextParams;
        }
        $routeDefinition = $this->getRouteCollection()->get($name);
        if (null !== $routeDefinition) {
            foreach ($params as $param => $value) {
                if (preg_match(sprintf('/{%s}/', $param), $routeDefinition->getPath())) {
                    $contextParams[$param] = $value;
                }
            }
        }
        return $contextParams;
    }
}
