<?php

namespace WS\Core\Library\Router;

use Symfony\Component\Routing\Generator\UrlGenerator;
use WS\Core\Service\NavigationService;
use WS\Core\Library\Router\Loader\Loader;
use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Router extends BaseRouter
{
    /** @var Loader */
    protected $loader;
    protected $defaultLocale;
    /** @var NavigationService */
    protected $navigationService;

    public function __construct()
    {
        call_user_func_array(array('Symfony\Bundle\FrameworkBundle\Routing\Router', '__construct'), func_get_args());
    }

    public function setNavigationService(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    public function setLoader(Loader $loader)
    {
        $this->loader = $loader;
    }

    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        // determine the most suitable locale to use for route generation
        $locale = $this->getLocale($parameters);

        // define parameters based on loader needs
        $parameters = array_merge(
            $parameters,
            $this->loader->getParameters($this->context)
        );

        $generator = $this->getGenerator();

        try {
            // let symfony generate the route
            return $generator->generate($name, $parameters, $referenceType);
        } catch (RouteNotFoundException $e) {
            // Try with the providers
            if ($this->navigationService) {
                if ($this->navigationService->hasRoute($name)) {

                    if (UrlGenerator::ABSOLUTE_URL === $referenceType || UrlGenerator::NETWORK_PATH === $referenceType) {
                        return sprintf('%s://%s%s',
                            $generator->getContext()->getScheme(),
                            $generator->getContext()->getHost(),
                            $this->navigationService->generateRoute($name, $parameters)
                        );
                    }

                    return $this->navigationService->generateRoute($name, $parameters);
                }
            }
        }

        // let ws generate the route
        $wsName = sprintf('%s/%s', $name, $locale);
        try {
            return $generator->generate($wsName, $parameters, $referenceType);
        } catch (RouteNotFoundException $e) {
            // Try with the providers
            if ($this->navigationService->hasRoute($wsName)) {
                if (UrlGenerator::ABSOLUTE_URL === $referenceType || UrlGenerator::NETWORK_PATH === $referenceType) {
                    return sprintf('%s://%s%s',
                        $generator->getContext()->getScheme(),
                        $generator->getContext()->getHost(),
                        $this->navigationService->generateRoute($wsName, $parameters)
                    );
                }
                return $this->navigationService->generateRoute($wsName, $parameters);
            }
        }

        throw new RouteNotFoundException(sprintf('Route "%s" not found', $name));
    }

    public function getRouteCollection()
    {
        return $this->loader->load(parent::getRouteCollection());
    }

    protected function getLocale(?array $parameters = [])
    {
        $currentLocale = $this->context->getParameter('_locale');
        if (isset($parameters['_locale'])) {
            $locale = $parameters['_locale'];
        } elseif ($currentLocale) {
            $locale = $currentLocale;
        } else {
            $locale = $this->defaultLocale;
        }

        return $locale;
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
