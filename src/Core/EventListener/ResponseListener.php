<?php

namespace WS\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use WS\Core\Service\ContextInterface;

#[AsEventListener(event: ResponseEvent::class, method: 'onResponse', priority: -512)]
class ResponseListener
{
    public function __construct(
        private array $config,
        private ContextInterface $context
    ) {
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // CMS customs
        if ($this->context->isCMS()) {
            if ($this->config['cache_control_directive']) {
                $event->getResponse()->setCache([
                    'private' => true
                ]);
                $event->getResponse()->headers->addCacheControlDirective('no-store');
            }
        }

        if ($this->config['powered_by']) {
            // PoweredBy headers
            $event->getResponse()->headers->set('X-Powered-By', 'WideStand by Sngular');
        }

        if ($this->config['content_type_options']) {
            // Security headers
            $event->getResponse()->headers->set('X-Content-Type-Options', 'nosniff');
        }
    }
}
