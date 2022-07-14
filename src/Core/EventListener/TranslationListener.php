<?php

namespace WS\Core\EventListener;

use WS\Core\Service\ContextService;
use WS\Core\Service\TranslationService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Translation\DataCollectorTranslator;

class TranslationListener
{
    protected TranslatorInterface $translator;
    protected ContextService $contextService;
    protected TranslationService $translationService;

    public function __construct(
        TranslatorInterface $translator,
        ContextService $contextService,
        TranslationService $translationService
    ) {
        $this->translator = $translator;
        $this->contextService = $contextService;
        $this->translationService = $translationService;
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
        $translator->setLocale($this->contextService->getDomain()->getLocale());

        $catalogue = $translator->getCatalogue($this->contextService->getDomain()->getLocale());
        $this->translationService->fillCatalogue($catalogue);
    }
}
