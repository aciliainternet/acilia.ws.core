<?php

namespace WS\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextInterface;

#[AsEventListener(event: RequestEvent::class, method: 'setupLocale', priority: 99)]
class LocaleListener
{
    public const SESSION_CMS_LOCALE = 'ws_cms_locale';

    public function __construct(private ContextInterface $context)
    {
    }

    public function setupLocale(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->context->getDomain() instanceof Domain) {
            return;
        }

        if (!empty($this->context->getDomain()->getCulture())) {
            $locale = sprintf(
                '%s.UTF-8',
                str_replace('-', '_', $this->context->getDomain()->getCulture())
            );

            setlocale(LC_TIME, $locale);
            setlocale(LC_COLLATE, $locale);
            setlocale(LC_MONETARY, $locale);
        }

        if (!empty($this->context->getDomain()->getTimezone())) {
            date_default_timezone_set($this->context->getDomain()->getTimezone());
        }

        if ($this->context->isCMS()) {
            // Get session from request
            $session = $event->getRequest()->getSession();

            if ($session !== null && $session->has(self::SESSION_CMS_LOCALE)) {
                $event->getRequest()->setLocale(strval($session->get(self::SESSION_CMS_LOCALE)));
                $event->getRequest()->setDefaultLocale(strval($session->get(self::SESSION_CMS_LOCALE)));
            }

            return;
        }

        $event->getRequest()->setLocale($this->context->getDomain()->getLocale());
        $event->getRequest()->setDefaultLocale($this->context->getDomain()->getLocale());
    }
}
