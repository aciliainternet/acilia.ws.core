<?php

namespace WS\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\TranslationService;

#[AsEventListener(event: RequestEvent::class, method: 'onRequest', priority: 90)]
readonly class TranslationListener
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected ContextInterface $context,
        protected TranslationService $translationService
    ) {
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->context->isCMS()) {
            return;
        }

        /** @var DataCollectorTranslator $translator*/
        $translator = $this->getTranslator();

        /** @var Domain $domain */
        $domain = $this->context->getDomain();
        $translator->setLocale($domain->getLocale());

        $catalogue = $translator->getCatalogue($domain->getLocale());
        $this->translationService->fillCatalogue($catalogue);
    }
}
