<?php

namespace WS\Core\EventListener;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use WS\Core\Service\ContextInterface;

#[AsEventListener(event: RequestEvent::class, method: 'setupAssetPackage', priority: 125)]
class AssetPackageListener
{
    public function __construct(
        private ParameterBagInterface $params,
        private Packages $packages,
        private ContextInterface $context
    ) {
    }

    public function setupAssetPackage(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!($this->context->isCMS() || $this->context->isSite())) {
            return;
        }

        $jsonManifestPath = \sprintf(
            '%s/public/bundles/wscore/manifest.json',
            \strval($this->params->get('kernel.project_dir'))
        );
        $this->packages->addPackage('core', new Package(new JsonManifestVersionStrategy($jsonManifestPath)));
    }
}
