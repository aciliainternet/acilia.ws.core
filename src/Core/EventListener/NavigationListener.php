<?php

namespace WS\Core\EventListener;

use WS\Core\Service\ContextService;
use WS\Core\Service\NavigationService;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NavigationListener
{
    protected $contextService;
    protected $navigationService;
    protected $kernel;

    public function __construct(ContextService $contextService, NavigationService $navigationService, HttpKernelInterface $kernel)
    {
        $this->contextService = $contextService;
        $this->navigationService = $navigationService;
        $this->kernel = $kernel;
    }

    public function onException(ExceptionEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->contextService->isSite()) {
            return;
        }

        // get the exception object from the received event
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            $attributes = $this->navigationService->resolvePath($event->getRequest()->getPathInfo());
            if (is_array($attributes)) {
                $request = $event->getRequest();
                $subRequest = $request->duplicate([], null, $attributes);
                $response = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
                $event->allowCustomResponseCode();
                $event->setResponse($response);
            }
        }
    }
}
