<?php

namespace WS\Core\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigFunction;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WS\Core\Entity\Domain;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Service\AlertService;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\DashboardService;
use WS\Core\Service\SettingService;

class ToolsExtension
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly AlertService $alertService,
        private readonly SettingService $settingService,
        private readonly DashboardService $dashboardService,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[AsTwigFunction(name: 'get_current_domain')]
    public function getCurrentDomain(): ?Domain
    {
        return $this->context->getDomain();
    }

    #[AsTwigFunction(name: 'get_domains')]
    public function getDomains(): array
    {
        return $this->context->getDomains();
    }

    #[AsTwigFunction(name: 'get_locale_domain')]
    public function getLocaleDomain(string $locale): ?Domain
    {
        foreach ($this->context->getDomains() as $domain) {
            if ($domain->getLocale() === $locale) {
                return $domain;
            }
        }

        return null;
    }

    #[AsTwigFunction(name: 'has_locale_domain')]
    public function hasLocaleDomain(string $locale): bool
    {
        foreach ($this->context->getDomains() as $domain) {
            if ($domain->getLocale() === $locale) {
                return true;
            }
        }

        return false;
    }

    #[AsTwigFunction(name: 'has_alerts')]
    public function hasAlerts(): bool
    {
        $alerts = $this->alertService->getAlerts();
        return count($alerts) > 0;
    }

    #[AsTwigFunction(name: 'get_alerts')]
    public function getAlerts(): array
    {
        return $this->alertService->getAlerts();
    }

    #[AsTwigFunction(name: 'get_setting')]
    public function getSetting(string $setting): ?string
    {
        return $this->settingService->get($setting);
    }

    #[AsTwigFunction(name: 'get_setting_sections')]
    public function getSettingSections(): array
    {
        return $this->settingService->getSections();
    }

    #[AsTwigFunction(name: 'get_form_theme')]
    public function getFormTheme(): string
    {
        if ($this->context->isCMS()) {
            return '@WSCore/cms/form/fields.html.twig';
        }

        return 'form_div_layout.html.twig';
    }

    #[AsTwigFunction(name: 'get_filter_query')]
    public function getFilterQuery(array $queryParams, array $filters): string
    {
        $filterPath = '';
        foreach ($filters as $filter) {
            if (isset($queryParams[$filter])) {
                $filterPath = sprintf('%s%s', $filterPath, sprintf('&%s=%s', $filter, $queryParams[$filter]));
            }
        }

        return $filterPath;
    }

    #[AsTwigFunction(name: 'get_batch_action_data')]
    public function getBatchActionData(string $action): ?array
    {
        return match ($action) {
            AbstractController::DELETE_BATCH_ACTION => [
                'label' => 'delete',
                'route' => 'batch_delete',
                'title' => 'batch_action.remove_alert_title'
            ],
            default => null,
        };
    }

    #[AsTwigFunction(name: 'get_dashboard_widgets')]
    public function getDashboardWidgets(): array
    {
        return $this->dashboardService->getWidgets();
    }

    #[AsTwigFunction(name: 'render_dashboard_widget', isSafe: ['html'])]
    public function renderDashboardWidget(string $widget): string
    {
        return $this->dashboardService->render($widget);
    }

    #[AsTwigFilter(name: 'time_diff', needsEnvironment: true)]
    public function getTimeDiff(Environment $env, \DateTimeInterface $date, ?string $now = null): string
    {
        // Convert both dates to DateTime instances.
        $date = $env->getExtension(CoreExtension::class)->convertDate($date);
        $now =  $env->getExtension(CoreExtension::class)->convertDate($now);


        // Get the difference between the two DateTime objects.
        $diff = $date->diff($now);

        $units = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        // Check for each interval if it appears in the $diff object.
        foreach ($units as $attribute => $unit) {
            $count = $diff->$attribute;
            if (0 !== $count) {
                return $this->translator->trans(
                    \sprintf('diff.%s.%s', $diff->invert ? 'in' : 'ago', $unit),
                    ['%count%' => $count],
                    'ws_cms'
                );
            }
        }

        return '';
    }
}
