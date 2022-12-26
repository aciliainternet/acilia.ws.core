<?php

namespace WS\Core\Library\Router;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use WS\Core\Library\Router\Loader\LocalizationStrategyInterface;

class RoutingLoader extends Loader
{
    private bool $isLoaded = false;
    private LocalizationStrategyInterface $localizationStrategy;
    private string $projectDir;

    public function setProjectDir(string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }

    public function setLocalizationStrategy(LocalizationStrategyInterface $localizationStrategy): void
    {
        $this->localizationStrategy = $localizationStrategy;
    }

    public function supports($resource, string $type = null): bool
    {
        return 'ws_annotation' === $type;
    }

    public function getParameters(RequestContext $context): array
    {
        return $this->localizationStrategy->getParameters($context);
    }

    public function load(mixed $resource, string $type = null): RouteCollection
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException(sprintf('Do not add the "%s" loader twice', $type));
        }

        $routes = new RouteCollection();

        $type = 'annotation';
        $resource = sprintf('%s/%s', $this->projectDir, strval($resource));

        /** @var array */
        $importedRoutes = $this->import($resource, $type);
        foreach ($importedRoutes as $name => $route) {
            $routeOptions = $route->getOptions();

            if (isset($routeOptions['i18n'])) {
                $i18nOptions = $routeOptions['i18n'];
                unset($routeOptions['i18n']);

                foreach ($this->localizationStrategy->getLocales() as $locale) {
                    $i18nRoute = clone $route;
                    $i18nRoute->setOptions($routeOptions);

                    if (isset($i18nOptions[$locale])) {
                        $i18nRoute->setPath($i18nOptions[$locale]);
                    }

                    $this->localizationStrategy->localize($locale, $i18nRoute);

                    $routes->add(sprintf('%s/%s', $name, $locale), $i18nRoute);
                }
            } else {
                $routes->add($name, $route);
            }
        }

        $this->isLoaded = true;

        return $routes;
    }
}
