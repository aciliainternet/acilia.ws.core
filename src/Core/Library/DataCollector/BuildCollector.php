<?php

namespace WS\Core\Library\DataCollector;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WS\Core\Entity\Domain;
use WS\Core\Service\ContextService;

class BuildCollector extends DataCollector
{
    protected ParameterBagInterface $parameterBag;
    protected ContextService $contextService;
    protected array $components = [];

    public function __construct(ParameterBagInterface $parameterBag, ContextService $contextService)
    {
        $this->parameterBag = $parameterBag;
        $this->contextService = $contextService;
    }

    public function addComponent($component): void
    {
        $this->components[] = $component;
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $build = 'dev';
        if ($this->parameterBag->get('build') != '') {
            $build = $this->parameterBag->get('build');
        }

        $components = [];
        foreach ($this->components as $component) {
            $components[$component::NAME] = $component::VERSION;
        }

        $this->data = [
            'build' => $build,
            'domain' => $this->contextService->getDomain(),
            'components' => $components
        ];
    }

    public function reset(): void
    {
        $this->data = array();
    }

    public function getName(): string
    {
        return 'ws.build_collector';
    }

    public function getBuild(): string
    {
        return $this->data['build'];
    }

    public function getDomain(): ?Domain
    {
        return $this->data['domain'];
    }

    public function getComponents(): array
    {
        return $this->data['components'];
    }
}
