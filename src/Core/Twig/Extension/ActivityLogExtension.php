<?php

namespace WS\Core\Twig\Extension;

use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigFunction;
use Twig\Attribute\AsTwigTest;
use WS\Core\Service\ActivityLogService;

class ActivityLogExtension
{
    public function __construct(private ActivityLogService $activityLogService)
    {
    }

    #[AsTwigFunction(name: 'ws_activity_log_enabled')]
    public function isEnabled(): bool
    {
        return $this->activityLogService->isEnabled();
    }

    #[AsTwigFilter(name: 'ws_activity_log_model')]
    public function printModel(string $modelName): string
    {
        $classPath = explode('\\', $modelName);

        return $classPath[count($classPath) - 1];
    }

    #[AsTwigFilter(name: 'ws_activity_log_action')]
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

    #[AsTwigTest(name: 'ws_activity_log_selected')]
    public function selected(mixed $value, array $filter, string $key): bool
    {
        if (isset($filter[$key]) && $filter[$key] === $value) {
            return true;
        }

        return false;
    }
}
