<?php

namespace WS\Core\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;
use WS\Core\Service\ContextService;

class ExceptionListener
{
    protected ContextService $contextService;
    protected Environment $twigEnvironment;
    protected ParameterBagInterface $parameterBag;

    public function __construct(
        ContextService $contextService,
        Environment $twigEnvironment,
        ParameterBagInterface $parameterBag
    ) {
        $this->contextService = $contextService;
        $this->twigEnvironment = $twigEnvironment;
        $this->parameterBag = $parameterBag;
    }

    public function onException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->parameterBag->get('kernel.debug')) {
            return;
        }

        // get the exception object from the received event
        $exception = $event->getThrowable();
        if (!$exception instanceof HttpExceptionInterface) {
            return;
        }

        $code = $exception->getStatusCode();
        if (in_array($code, [
            Response::HTTP_FORBIDDEN,
            Response::HTTP_NOT_FOUND,
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_INTERNAL_SERVER_ERROR
        ])) {
            // define the template to show
            $template = sprintf(
                '@WSCore/%s/errors/error%s.html.twig',
                $this->contextService->getTemplatesBase(),
                $code
            );

            // create the response from the view environment
            $response = new Response($this->twigEnvironment->render($template, [
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception
            ]));

            // sends the modified response object to the event
            $event->setResponse($response);
        }
    }
}
