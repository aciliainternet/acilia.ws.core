<?php

namespace WS\Core\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;
use WS\Core\Service\ContextInterface;

#[AsEventListener(event: ExceptionEvent::class, method: 'onException', priority: 124)]
class ExceptionListener
{
    public function __construct(
        private ContextInterface $context,
        private Environment $twigEnvironment,
        private ParameterBagInterface $parameterBag
    ) {
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

        if ($this->context->isSite()) {
            $code = 500;
            if ($exception instanceof HttpExceptionInterface) {
                $code = $exception->getStatusCode();
                if (!in_array($code, [
                    Response::HTTP_FORBIDDEN,
                    Response::HTTP_NOT_FOUND,
                    Response::HTTP_UNAUTHORIZED,
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ])) {
                    $code = 500;
                }
            }

            $template = sprintf('site/errors/error%s.html.twig', $code);
            if ($this->twigEnvironment->getLoader()->exists($template)) {
                // create the response from the view environment
                $response = new Response($this->twigEnvironment->render($template, [
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception' => $exception
                ]));

                // sends the modified response object to the event
                $event->setResponse($response);
            }

            return;
        }


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
            $template = sprintf('@WSCore/cms/errors/error%s.html.twig', $code);

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
