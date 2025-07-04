<?php

namespace WS\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFilter;
use WS\Core\Service\Entity\AdministratorService;

class AdministratorExtension
{
    public function __construct(
        protected AdministratorService $administratorService,
        protected TranslatorInterface $translator
    ) {
    }

    #[AsTwigFilter(name: 'ws_cms_administrator_profile')]
    public function getProfile(string $profile): string
    {
        return $this->translator->trans($this->administratorService->getProfileLabel($profile), [], 'cms');
    }
}
