<?php

namespace WS\Core\EventListener;

use WS\Core\Service\ContextService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TemplateListener
{
    public function __construct(
        protected Environment $twigEnvironment,
        protected ParameterBagInterface $parameterBagInterface,
        protected ContextService $contextService
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

        if (!($this->contextService->isCMS() || $this->contextService->isSite())) {
            return;
        }

        // Setup Twig paths for the current context
        $twigPaths = $this->getTwigLoader()->getPaths();

        $newPath = sprintf(
            '%s/templates/%s',
            \strval($this->parameterBagInterface->get('kernel.project_dir')),
            $this->contextService->getTemplatesBase()
        );
        array_unshift($twigPaths, $newPath);

        $this->getTwigLoader()->setPaths($twigPaths);
    }
}
