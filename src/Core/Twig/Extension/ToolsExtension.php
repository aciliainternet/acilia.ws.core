<?php

namespace WS\Core\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Core\Entity\Domain;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Library\Dashboard\DashboardWidgetInterface;
use WS\Core\Service\AlertService;
use WS\Core\Service\ContextService;
use WS\Core\Service\DashboardService;
use WS\Core\Service\SettingService;

class ToolsExtension extends AbstractExtension
{
    public function __construct(
        protected ContextService $contextService,
        protected AlertService $alertService,
        protected SettingService $settingService,
        protected DashboardService $dashboardService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_domain', [$this, 'getCurrentDomain']),
            new TwigFunction('get_locale_domain', [$this, 'getLocaleDomain']),
            new TwigFunction('has_locale_domain', [$this, 'hasLocaleDomain']),
            new TwigFunction('get_domains', [$this, 'getDomains']),
            new TwigFunction('has_alerts', [$this, 'hasAlerts']),
            new TwigFunction('get_alerts', [$this, 'getAlerts']),
            new TwigFunction('get_setting', [$this, 'getSetting']),
            new TwigFunction('get_setting_sections', [$this, 'getSettingSections']),
            new TwigFunction('get_form_theme', [$this, 'getFormTheme']),
            new TwigFunction('get_filter_query', [$this, 'getFilterQuery']),
            new TwigFunction('get_batch_action_data', [$this, 'getBatchActionData']),
            new TwigFunction('get_dashboard_widgets', [$this, 'getDashboardWidgets']),
            new TwigFunction('render_dashboard_widget', [$this, 'renderDashboardWidget'], ['is_safe' => ['html']])
        ];
    }

    public function getCurrentDomain(): ?Domain
    {
        return $this->contextService->getDomain();
    }

    public function getDomains(): array
    {
        return $this->contextService->getDomains();
    }

    public function getLocaleDomain(string $locale): ?Domain
    {
        foreach ($this->contextService->getDomains() as $domain) {
            if ($domain->getLocale() === $locale) {
                return $domain;
            }
        }

        return null;
    }

    public function hasLocaleDomain(string $locale): bool
    {
        foreach ($this->contextService->getDomains() as $domain) {
            if ($domain->getLocale() === $locale) {
                return true;
            }
        }

        return false;
    }

    public function hasAlerts(): bool
    {
        $alerts = $this->alertService->getAlerts();
        return count($alerts) > 0;
    }

    public function getAlerts(): array
    {
        return $this->alertService->getAlerts();
    }

    public function getSetting(string $setting): ?string
    {
        return $this->settingService->get($setting);
    }

    public function getSettingSections(): array
    {
        return $this->settingService->getSections();
    }

    public function getFormTheme(): string
    {
        if ($this->contextService->isCMS()) {
            return '@WSCore/cms/form/fields.html.twig';
        }

        return 'form_div_layout.html.twig';
    }

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

    public function getBatchActionData(string $action): ?array
    {
        switch ($action) {
            case AbstractController::DELETE_BATCH_ACTION:
                return [
                    'label' => 'delete',
                    'route' => 'batch_delete',
                    'title' => 'batch_action.remove_alert_title'
                ];
            default:
                return null;
        }
    }

    public function getDashboardWidgets(): array
    {
        return $this->dashboardService->getWidgets();
    }

    public function renderDashboardWidget(DashboardWidgetInterface $widget): string
    {
        return $this->dashboardService->render($widget->getId());
    }
}
