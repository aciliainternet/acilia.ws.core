<?php

namespace WS\Core\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextService;

class LocaleListener
{
    public const SESSION_CMS_LOCALE = 'ws_cms_locale';

    public function __construct(private ContextService $contextService)
    {
    }

    public function setupLocale(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->contextService->getDomain() instanceof Domain) {
            return;
        }

        if (!empty($this->contextService->getDomain()->getCulture())) {
            $locale = sprintf(
                '%s.UTF-8',
                str_replace('-', '_', $this->contextService->getDomain()->getCulture())
            );

            setlocale(LC_TIME, $locale);
            setlocale(LC_COLLATE, $locale);
            setlocale(LC_MONETARY, $locale);
        }

        if (!empty($this->contextService->getDomain()->getTimezone())) {
            date_default_timezone_set($this->contextService->getDomain()->getTimezone());
        }

        if ($this->contextService->isCMS()) {
            // Get session from request
            $session = $event->getRequest()->getSession();

            if ($session !== null && $session->has(self::SESSION_CMS_LOCALE)) {
                $event->getRequest()->setLocale(strval($session->get(self::SESSION_CMS_LOCALE)));
                $event->getRequest()->setDefaultLocale(strval($session->get(self::SESSION_CMS_LOCALE)));
            }

            return;
        }

        $event->getRequest()->setLocale($this->contextService->getDomain()->getLocale());
        $event->getRequest()->setDefaultLocale($this->contextService->getDomain()->getLocale());
    }
}
