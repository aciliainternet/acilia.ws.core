<?php

namespace WS\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\PreviewService;

#[AsEventListener(event: RequestEvent::class, method: 'onRequest')]
class PreviewListener
{
    public function __construct(
        protected PreviewService $previewService,
        protected ContextInterface $context,
    ) {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->context->isCMS()) {
            return;
        }

        if (!$this->previewService->isEnabled()) {
            return;
        }

        $request = $event->getRequest();

        /** @var string $preview; */
        $preview = $request->query->get($this->previewService->getQuery(), '');
        $this->previewService->unHash($preview.'1');
    }
}
