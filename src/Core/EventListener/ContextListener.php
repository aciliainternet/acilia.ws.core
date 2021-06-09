<?php

namespace WS\Core\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextService;
use WS\Core\Service\DomainService;
use WS\Core\Service\SettingService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ContextListener
{
    protected $twigEnvironment;
    protected $parameterBagInterface;
    protected $contextService;
    protected $domainService;
    protected $settingService;

    public function __construct(ContextService $contextService, DomainService $domainService, SettingService $settingService)
    {
        $this->contextService = $contextService;
        $this->domainService = $domainService;
        $this->settingService = $settingService;
    }

    public function setupDomain(RequestEvent $event)
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
                $domainId = $session->get(ContextService::SESSION_DOMAIN);

                $domain = $this->domainService->get($domainId);
                if ($domain instanceof Domain) {
                    $this->contextService->setDomain($domain);
                    $this->settingService->loadSettings();
                    return;
                } else {
                    throw new \Exception(sprintf('Domain with ID "%d" not found. Clean your cookies.', $domainId));
                }
            }
        }

        // Detect Domain
        $domains = $this->domainService->getByHost($event->getRequest()->getHost());
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
            // Domain is Locale dependant
        } else {
            if ($this->contextService->isCMS()) {
                /** @var Domain $domain */
                $domain = $domains[0];
                $this->contextService->setDomain($domain);

                if ($session !== null) {
                    $session->set(ContextService::SESSION_DOMAIN, $domain->getId());
                }
            } else {
                $domainFound = false;
                /** @var Domain $domain */
                foreach ($domains as $domain) {
                    if (preg_match(sprintf('|^/%s/|i', $domain->getLocale()), $event->getRequest()->getPathInfo())) {
                        $this->contextService->setDomain($domain);
                        $this->settingService->loadSettings();
                        $domainFound = true;

                        if ($domain->getLocale() !== null) {
                            $event->getRequest()->setLocale($domain->getLocale());
                        }
                    }
                }

                if ($domainFound === false) {
                    throw new \Exception('Unable to setup domain into context.');
                }
            }
        }
    }
}
