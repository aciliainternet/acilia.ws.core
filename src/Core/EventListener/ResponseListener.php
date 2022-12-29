<?php

namespace WS\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use WS\Core\Service\ContextService;

#[AsEventListener(event: ResponseEvent::class, method: 'onResponse', priority: -512)]
class ResponseListener
{
    public function __construct(private ContextService $contextService)
    {
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // CMS customs
        if ($this->contextService->isCMS()) {
            $event->getResponse()->setCache([
                'private' => true
            ]);
            $event->getResponse()->headers->addCacheControlDirective('no-store');
        }

        // Generic tweaks
        $event->getResponse()->headers->set('X-Powered-By', 'WideStand by Acilia');

        // Security headers
        $event->getResponse()->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
