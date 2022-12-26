<?php

namespace WS\Core\Twig\Extension;

use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use WS\Core\Library\Router\Router;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CRUDExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $generator,
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

        if ($this->generator instanceof Router) {
            $parameters = array_merge(
                $this->generator->getContextParams($name, (array) $request->attributes->get('_route_params')),
                $parameters
            );
        }

        return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
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
        /** @var TwigFilter */
        $twigFilter = $environment->getFilter($filter);
        if ($twigFilter instanceof TwigFilter) {
            if(\is_callable($twigFilter->getCallable())){
                /** @var ?string */
                $filteredValue = call_user_func_array($twigFilter->getCallable(), [$value, $options]);
    
                $safeContext = $twigFilter->getSafe(new \Twig\Node\Node());
                if (!is_array($safeContext) || !in_array('html', $safeContext)) {
                    /** @var TwigFilter */
                    $escapeFilter = $environment->getFilter('escape');
                    if(\is_callable($escapeFilter->getCallable())){
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
