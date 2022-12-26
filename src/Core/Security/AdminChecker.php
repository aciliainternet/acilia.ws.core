<?php

namespace WS\Core\Security;

use WS\Core\Entity\Administrator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
