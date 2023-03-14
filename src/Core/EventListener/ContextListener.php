<?php

namespace WS\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\DomainInterface;
use WS\Core\Service\SettingService;

#[AsEventListener(event: RequestEvent::class, method: 'setupDomain', priority: 127)]
class ContextListener
{
    public function __construct(
        private ContextInterface $context,
        private DomainInterface $domainService,
        private SettingService $settingService
    ) {
    }

    public function setupDomain(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            $domain = $this->context->getDomain();
            if ($domain !== null && $domain->getLocale() !== null) {
                $event->getRequest()->setLocale($domain->getLocale());
            }

            return;
        }

        // Get session from request
        $session = $event->getRequest()->getSession();

        // Setup App Context
        $path = $event->getRequest()->getPathInfo();
        if (strpos($path, '/cms') === 0) {
            $this->context->setContext(ContextInterface::CMS);
        } elseif (strpos($path, '/_wdt') === 0 || strpos($path, '/_profiler') === 0) {
            $this->context->setContext(ContextInterface::SYMFONY);
        } else {
            $this->context->setContext(ContextInterface::SITE);
        }

        // Load Domain from Session for the CMS
        if ($this->context->isCMS()) {
            if ($session !== null && $session->has(ContextInterface::SESSION_DOMAIN)) {
                /** @var int */
                $domainId = $session->get(ContextInterface::SESSION_DOMAIN);

                $domain = $this->domainService->get($domainId);
                if ($domain instanceof Domain) {
                    $this->context->setDomain($domain);
                    $this->settingService->loadSettings();
                    return;
                } else {
                    throw new \Exception(sprintf('Domain with ID "%d" not found. Clean your cookies.', $domainId));
                }
            }
        }

        // Detect domains by host
        $domains = $this->domainService->getByHost($event->getRequest()->getHost());

        // If symfony context use default domain
        if (!$this->context->isCMS() && !$this->context->isSite()) {
            /** @var \WS\Core\Entity\Domain */
            $domain = \array_shift($domains);
            $this->context->setDomain($domain);
            return;
        }

        if (count($domains) == 1) {
            /** @var Domain $domain */
            $domain = $domains[0];
            $this->context->setDomain($domain);
            $this->settingService->loadSettings();

            if ($this->context->isCMS() && $event->getRequest()->getSession() instanceof SessionInterface) {
                if ($session !== null) {
                    $session->set(ContextInterface::SESSION_DOMAIN, $domain->getId());
                }
            } else {
                if ($domain->getLocale() !== null) {
                    $event->getRequest()->setLocale($domain->getLocale());
                }
            }
        } else {
            // Domain is locale dependant
            if ($this->context->isCMS()) {
                $domain = $domains[0];

                $this->context->setDomain($domain);
                $this->settingService->loadSettings();

                if ($session !== null) {
                    $session->set(ContextInterface::SESSION_DOMAIN, $domain->getId());
                }
            } else {
                $domain = null;
                foreach ($domains as $d) {
                    if (preg_match(
                        sprintf('#^/%s/|^/%s$#i', $d->getLocale(), $d->getLocale()),
                        $event->getRequest()->getPathInfo()
                    )) {
                        $domain = $d;
                        break;
                    }
                }

                if ($domain === null) {
                    // use the default domain
                    /** @var Domain $domain */
                    $domain = \array_shift($domains);
                }

                $this->context->setDomain($domain);
                $this->settingService->loadSettings();
                if ($domain->getLocale() !== null) {
                    $event->getRequest()->setLocale($domain->getLocale());
                }
            }
        }
    }
}
