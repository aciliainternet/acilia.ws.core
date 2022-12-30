<?php

namespace WS\Core\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextService;
use WS\Core\Service\DomainInterface;
use WS\Core\Service\SettingService;

class ContextListener
{
    public function __construct(
        private ContextService $contextService,
        private DomainInterface $domainInterface,
        private SettingService $settingService
    ) {
    }

    public function setupDomain(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            $domain = $this->contextService->getDomain();
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
            $this->contextService->setContext(ContextService::CMS);
        } elseif (strpos($path, '/_wdt') === 0 || strpos($path, '/_profiler') === 0) {
            $this->contextService->setContext(ContextService::SYMFONY);
        } else {
            $this->contextService->setContext(ContextService::SITE);
        }

        // Load Domain from Session for the CMS
        if ($this->contextService->isCMS()) {
            if ($session !== null && $session->has(ContextService::SESSION_DOMAIN)) {
                /** @var int */
                $domainId = $session->get(ContextService::SESSION_DOMAIN);

                $domain = $this->domainInterface->get($domainId);
                if ($domain instanceof Domain) {
                    $this->contextService->setDomain($domain);
                    $this->settingService->loadSettings();
                    return;
                } else {
                    throw new \Exception(sprintf('Domain with ID "%d" not found. Clean your cookies.', $domainId));
                }
            }
        }

        // Detect domains by host
        $domains = $this->domainInterface->getByHost($event->getRequest()->getHost());

        // If symfony context use default domain
        if (!$this->contextService->isCMS() && !$this->contextService->isSite()) {
            /** @var \WS\Core\Entity\Domain */
            $domain = \array_shift($domains);
            $this->contextService->setDomain($domain);
            return;
        }

        if (count($domains) == 1) {
            /** @var Domain $domain */
            $domain = $domains[0];
            $this->contextService->setDomain($domain);
            $this->settingService->loadSettings();

            if ($this->contextService->isCMS() && $event->getRequest()->getSession() instanceof SessionInterface) {
                if ($session !== null) {
                    $session->set(ContextService::SESSION_DOMAIN, $domain->getId());
                }
            } else {
                if ($domain->getLocale() !== null) {
                    $event->getRequest()->setLocale($domain->getLocale());
                }
            }
        } else {
            // Domain is locale dependant
            if ($this->contextService->isCMS()) {
                $domain = $domains[0];
                $this->contextService->setDomain($domain);

                if ($session !== null) {
                    $session->set(ContextService::SESSION_DOMAIN, $domain->getId());
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

                $this->contextService->setDomain($domain);
                $this->settingService->loadSettings();
                if ($domain->getLocale() !== null) {
                    $event->getRequest()->setLocale($domain->getLocale());
                }
            }
        }
    }
}
