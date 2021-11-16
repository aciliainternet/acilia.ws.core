<?php

namespace WS\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

class CRUDExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ws_cms_crud_list_is_date', [$this, 'listIsDate']),
            new TwigFunction('ws_cms_crud_list_filter', [$this, 'listFilter'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
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
