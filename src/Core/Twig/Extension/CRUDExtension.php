<?php

namespace WS\Core\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use WS\Core\Library\Router\Router;

class CRUDExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private UrlGeneratorInterface $generator;
    private TranslatorInterface $translator;

    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $generator,
        TranslatorInterface $translator
    ) {
        $this->requestStack = $requestStack;
        $this->generator = $generator;
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ws_cms_path', [$this, 'getPath']),
            new TwigFunction('ws_cms_crud_list_is_date', [$this, 'listIsDate']),
            new TwigFunction('ws_cms_crud_list_filter', [$this, 'listFilter'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function getPath(string $name, array $parameters = [], bool $relative = false): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        /** @var Router */
        $urlGenerator = $this->generator;

        $parameters = array_merge(
            $urlGenerator->getContextParams($name, (array) $request->get('_route_params')),
            $parameters
        );

        return $urlGenerator->generate(
            $name,
            $parameters,
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    public function listIsDate(?\DateTimeInterface $dateTime): string
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime->format($this->translator->trans('date_hour_format', [], 'ws_cms'));
        }

        return '-';
    }

    public function listFilter(Environment $environment, string $filter, array $options, string $value): ?string
    {
        $twigFilter = $environment->getFilter($filter);
        if ($twigFilter instanceof TwigFilter) {
            if (null === $twigFilter->getCallable()) {
                return null;
            }
            $filteredValue = call_user_func_array($twigFilter->getCallable(), [$value, $options]);

            $safeContext = $twigFilter->getSafe(new \Twig\Node\Node());
            if (!is_array($safeContext) || !in_array('html', $safeContext)) {
                $escapeFilter = $environment->getFilter('escape');
                if (null === $escapeFilter || null === $escapeFilter->getCallable()) {
                    return null;
                }
                $filteredValue = call_user_func($escapeFilter->getCallable(), $environment, $filteredValue);
            }

            return $filteredValue;
        }

        return $value;
    }
}
