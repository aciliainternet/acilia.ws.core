<?php

namespace WS\Core\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Service\ContextService;
use WS\Core\Service\TranslationService;

class TranslationListener
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected ContextService $contextService,
        protected TranslationService $translationService
    ) {
    }

    protected function getTranslator(): TranslatorInterface
    {
        $translator = $this->translator;

        return $translator;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->contextService->isCMS()) {
            return;
        }

        /** @var DataCollectorTranslator */
        $translator = $this->getTranslator();

        /** @var \WS\Core\Entity\Domain */
        $domain = $this->contextService->getDomain();
        $translator->setLocale($domain->getLocale());

        $catalogue = $translator->getCatalogue($domain->getLocale());
        $this->translationService->fillCatalogue($catalogue);
    }
}
