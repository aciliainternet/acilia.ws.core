<?php

namespace WS\Core\EventListener;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TemplateListener
{
    protected ParameterBagInterface $parameterBagInterface;
    protected Packages $packages;

    public function __construct(
        ParameterBagInterface $parameterBagInterface,
        Packages $packages
    ) {
        $this->parameterBagInterface = $parameterBagInterface;
        $this->packages = $packages;
    }

    public function setupAssetPackage(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!($this->contextService->isCMS() || $this->contextService->isSite())) {
            return;
        }

        $jsonManifestPath = \sprintf(
            '%s/public/bundles/wscore/manifest.json',
            \strval($this->parameterBagInterface->get('kernel.project_dir'))
        );
        $this->packages->addPackage('core', new Package(new JsonManifestVersionStrategy($jsonManifestPath)));
    }
}
