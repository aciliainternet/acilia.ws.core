<?php

namespace WS\Core\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CRUDExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $router,
        private TranslatorInterface $translator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ws_cms_path', [$this, 'getPath']),
            new TwigFunction('ws_cms_crud_list_is_date', [$this, 'listIsDate']),
            new TwigFunction('ws_cms_crud_list_filter', [$this, 'listFilter'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        /** @var Request */
        $request = $this->requestStack->getCurrentRequest();

        // fetch context params (if any)
        $routeParams = $request->attributes->get('_route_params');
        $contextParams = [];
        $routeDefinition = $this->router->getRouteCollection()->get($name);
        if (null !== $routeDefinition) {
            foreach ($routeParams as $param => $value) {
                if (preg_match(sprintf('/{%s}/', $param), $routeDefinition->getPath())) {
                    $contextParams[$param] = $value;
                }
            }
        }

        // merge with current params
        $parameters = array_merge($contextParams, $parameters);

        return $this->router->generate(
            $name,
            $parameters,
            $relative ? RouterInterface::RELATIVE_PATH : RouterInterface::ABSOLUTE_PATH
        );
    }

    public function listIsDate(?\DateTimeInterface $dateTime): string
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime->format($this->translator->trans('date_hour_format', [], 'ws_cms'));
        }

        return '-';
    }

    public function listFilter(Environment $environment, string $filter, array $options, mixed $value): ?string
    {
        /** @var TwigFilter */
        $twigFilter = $environment->getFilter($filter);
        if ($twigFilter instanceof TwigFilter) {
            if (\is_callable($twigFilter->getCallable())) {
                /** @var ?string */
                $filteredValue = call_user_func_array($twigFilter->getCallable(), [$value, $options]);

                $safeContext = $twigFilter->getSafe(new \Twig\Node\Node());
                if (!is_array($safeContext) || !in_array('html', $safeContext)) {
                    /** @var TwigFilter */
                    $escapeFilter = $environment->getFilter('escape');
                    if (\is_callable($escapeFilter->getCallable())) {
                        /** @var ?string */
                        $filteredValue = call_user_func($escapeFilter->getCallable(), $environment, $filteredValue);
                    }
                }

                return $filteredValue;
            }
        }

        return $value;
    }
}
