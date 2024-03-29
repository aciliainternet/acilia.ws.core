<?php

namespace WS\Core\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WS\Core\Service\ContextInterface;

#[AsEventListener(event: RequestEvent::class, method: 'setupTemplate', priority: 126)]
class TemplateListener
{
    public function __construct(
        private Environment $twigEnvironment,
        private ParameterBagInterface $parameterBagInterface,
        private ContextInterface $context
    ) {
    }

    protected function getTwigLoader(): FilesystemLoader
    {
        /** @var FilesystemLoader */
        $loader = $this->twigEnvironment->getLoader();

        return $loader;
    }

    public function setupTemplate(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!($this->context->isCMS() || $this->context->isSite())) {
            return;
        }

        // Setup Twig paths for the current context
        $twigPaths = $this->getTwigLoader()->getPaths();

        $newPath = sprintf(
            '%s/templates/%s',
            \strval($this->parameterBagInterface->get('kernel.project_dir')),
            $this->context->getTemplatesBase()
        );

        if (\file_exists($newPath)) {
            array_unshift($twigPaths, $newPath);
        }

        $this->getTwigLoader()->setPaths($twigPaths);
    }
}
