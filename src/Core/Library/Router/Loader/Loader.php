<?php


namespace WS\Core\Library\Router\Loader;

use WS\Core\Service\NavigationService;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Loader
{
    private LocalizationStrategyInterface $localizationStrategy;
    private NavigationService $navigationService;

    public function __construct(LocalizationStrategyInterface $localizationStrategy, NavigationService $navigationService)
    {
        $this->localizationStrategy = $localizationStrategy;
        $this->navigationService = $navigationService;
    }

    public function getParameters(RequestContext $context): array
    {
        return $this->localizationStrategy->getParameters($context);
    }

    public function load(RouteCollection $collection): RouteCollection
    {
        // Process routes and create new translated routes
        foreach ($collection->all() as $name => $route) {
            $routeOptions = $route->getOptions();

            if (isset($routeOptions['i18n'])) {
                $collection->remove($name);
                $i18nOptions = $routeOptions['i18n'];
                unset($routeOptions['i18n']);

                foreach ($this->localizationStrategy->getLocales() as $locale) {
                    $i18nRoute = clone $route;
                    $i18nRoute->setOptions($routeOptions);

                    if (isset($i18nOptions[$locale])) {
                        $i18nRoute->setPath($i18nOptions[$locale]);
                    }

                    $this->localizationStrategy->localize($locale, $i18nRoute);

                    $collection->add(sprintf('%s/%s', $name, $locale), $i18nRoute);
                }
            }
        }

        // Process Navigational routes
        foreach ($collection->all() as $name => $route) {
            $routeOptions = $route->getOptions();
            if (isset($routeOptions['navigation']) && $routeOptions['navigation'] === true) {
                $this->navigationService->addNavigation($name, $route);
                $collection->remove($name);
            }
        }

        $this->navigationService->compileNavigations();

        return $collection;
    }
}
