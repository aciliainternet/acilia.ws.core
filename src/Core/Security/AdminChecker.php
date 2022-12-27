<?php

namespace WS\Core\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Entity\Administrator;

class AdminChecker implements UserCheckerInterface
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function checkPreAuth(UserInterface $administrator): void
    {
        if (!$administrator instanceof Administrator) {
            return;
        }

        // administrator is disabled
        if (!$administrator->isActive()) {
            throw new BadCredentialsException($this->translator->trans('login.disabled_admin', [], 'ws_cms'));
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
