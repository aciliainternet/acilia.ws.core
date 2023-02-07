<?php

namespace WS\Core\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WS\Core\Entity\Administrator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use WS\Core\Service\RecaptchaService;
use WS\Core\Service\SettingService;

class AdminChecker implements UserCheckerInterface
{
    protected $translator;
    protected SettingService $settingService;
    protected RecaptchaService $recaptchaService;
    protected RequestStack $requestStack;

    public function __construct(
        TranslatorInterface $translator,
        SettingService $settingService,
        RecaptchaService $recaptchaService,
        RequestStack $requestStack
    ) {
        $this->translator = $translator;
        $this->settingService = $settingService;
        $this->recaptchaService = $recaptchaService;
        $this->requestStack = $requestStack;
    }

    public function checkPreAuth(UserInterface $administrator)
    {
        if (!$this->validateRecaptchaToken($this->requestStack->getCurrentRequest())) {
            return;
        }

        if (!$administrator instanceof Administrator) {
            return;
        }

        // administrator is disabled
        if (!$administrator->isActive()) {
            throw new BadCredentialsException($this->translator->trans('login.disabled_admin', [], 'ws_cms'));
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }

    private function validateRecaptchaToken(Request $request): bool
    {
        $recaptchaKey = $this->settingService->get('recaptcha_key');
        $recaptchaSecret = $this->settingService->get('recaptcha_secret_key');
        if ($recaptchaSecret !== null && $recaptchaSecret !== null) {
            $gRecaptchaResponse = $request->get('g-recaptcha-response');
            return $this->recaptchaService->verify($gRecaptchaResponse);
        }
        return true;
    }
}
