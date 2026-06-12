<?php

namespace WS\Core\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFunction;
use Twig\Environment;
use Twig\Node\EmptyNode;
use Twig\TwigFilter;

class CRUDExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[AsTwigFunction(name: 'ws_cms_path')]
    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        // fetch context params (if any)
        /** @var array $routeParams*/
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
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    #[AsTwigFunction(name: 'ws_cms_crud_list_is_date')]
    public function listIsDate(?\DateTimeInterface $dateTime): string
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime->format($this->translator->trans('date_hour_format', [], 'ws_cms'));
        }

        return '-';
    }

    #[AsTwigFunction(name: 'ws_cms_crud_filter', needsEnvironment: true, isSafe: ['html'])]
    public function crudFilter(Environment $environment, string $filter, array $options, mixed $value): mixed
    {
        /** @var TwigFilter $twigFilter */
        $twigFilter = $environment->getFilter($filter);
        if ($twigFilter instanceof TwigFilter) {
            if (\is_callable($twigFilter->getCallable())) {
                /** @var ?string $filteredValue */
                $filteredValue = call_user_func_array($twigFilter->getCallable(), [$value, $options]);

                $safeContext = $twigFilter->getSafe(new EmptyNode());
                if (!is_array($safeContext) || !in_array('html', $safeContext)) {
                    /** @var TwigFilter $escapeFilter */
                    $escapeFilter = $environment->getFilter('escape');
                    if (\is_callable($escapeFilter->getCallable())) {
                        /** @var ?string $filteredValue */
                        $filteredValue = call_user_func($escapeFilter->getCallable(), $environment, $filteredValue);
                    }
                }

                return $filteredValue;
            }
        }

        return $value;
    }
}
