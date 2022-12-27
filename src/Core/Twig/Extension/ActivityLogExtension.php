<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use WS\Core\Service\ActivityLogService;

class ActivityLogExtension extends AbstractExtension
{
    public function __construct(private ActivityLogService $activityLogService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ws_activity_log_enabled', [$this, 'isEnabled']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ws_activity_log_model', [$this, 'printModel']),
            new TwigFilter('ws_activity_log_action', [$this, 'printActionClass'])
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('ws_activity_log_selected', [$this, 'selected']),
        ];
    }

    public function isEnabled(): bool
    {
        return $this->activityLogService->isEnabled();
    }

    public function printModel(string $modelName): string
    {
        $classPath = explode('\\', $modelName);

        return $classPath[count($classPath) - 1];
    }

    public function printActionClass(string $action): string
    {
        switch ($action) {
            case 'create':
                return 'success';
            case 'update':
                return 'info';
            default:
                return 'danger';
        }
    }

    public function selected(mixed $value, array $filter, string $key): bool
    {
        if (isset($filter[$key]) && $filter[$key] === $value) {
            return true;
        }

        return false;
    }
}
