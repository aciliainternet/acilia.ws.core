<?php

namespace WS\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use WS\Core\Service\Entity\AdministratorService;

class AdministratorExtension extends AbstractExtension
{
    protected AdministratorService $administratorService;
    protected TranslatorInterface $translator;

    public function __construct(AdministratorService $administratorService, TranslatorInterface $translator)
    {
        $this->administratorService = $administratorService;
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ws_cms_administrator_profile', [$this, 'getProfile']),
        ];
    }

    public function getProfile(string $profile): string
    {
        return $this->translator->trans($this->administratorService->getProfileLabel($profile), [], 'cms');
    }
}
