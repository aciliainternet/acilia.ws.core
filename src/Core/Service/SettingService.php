<?php

namespace WS\Core\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Entity\Setting;
use WS\Core\Library\Alert\AlertGathererInterface;
use WS\Core\Library\Alert\AlertMessage;
use WS\Core\Library\Alert\GatherAlertsEvent;
use WS\Core\Library\Setting\SettingDefinitionInterface;
use WS\Core\Library\Setting\Definition\Section as SectionDefinition;
use WS\Core\Library\Setting\Definition\Setting as SettingDefinition;

class SettingService implements AlertGathererInterface
{
    /** @var SectionDefinition[] */
    protected array $settings;
    protected ?array $settingValues = null;

    protected TranslatorInterface $translator;
    protected ManagerRegistry $registry;
    protected ContextService $contextService;

    public function __construct(
        TranslatorInterface $translator,
        ManagerRegistry $registry,
        ContextService $contextService
    ) {
        $this->translator = $translator;
        $this->registry = $registry;
        $this->contextService = $contextService;
    }

    public function registerSettingDefinition(SettingDefinitionInterface $service): void
    {
        foreach ($service->getSettingsDefinition() as $sectionDefinition) {
            if (!isset($this->settings[$sectionDefinition->getCode()])) {
                $this->settings[$sectionDefinition->getCode()] = $sectionDefinition;
            } else {
                $section = $this->settings[$sectionDefinition->getCode()];
                foreach ($sectionDefinition->getGroups() as $group) {
                    $section->addGroup($group);
                }
            }
        }
    }

    /**
     * @return SectionDefinition|null
     */
    public function getSection(string $section): ?SectionDefinition
    {
        if (isset($this->settings[$section])) {
            return $this->settings[$section];
        }

        return null;
    }

    /**
     * @return SectionDefinition[]
     */
    public function getSections(): array
    {
        $sections = $this->settings;
        uasort($sections, function (SectionDefinition $section1, SectionDefinition $section2) {
            if ($section1->getOrder() === $section2->getOrder()) {
                return 0;
            }

            return $section1->getOrder() > $section2->getOrder() ? 1 : -1;
        });

        return $sections;
    }

    /**
     * @return SettingDefinition[]
     */
    public function getSettingsByGroup(string $section, string $group): array
    {
        if (isset($this->settings[$section])) {
            foreach ($this->settings[$section]->getGroups() as $grp) {
                if ($grp->getCode() === $group) {
                    foreach ($grp->getSettings() as &$setting) {
                        if (isset($this->settingValues[$setting->getCode()])) {
                            $setting->setValue($this->settingValues[$setting->getCode()]);
                        }
                    }

                    return $grp->getSettings();
                }
            }
        }

        return [];
    }

    public function gatherAlerts(GatherAlertsEvent $event): void
    {
        $definedSettings = count(is_array($this->settingValues) ? $this->settingValues : []);
        $registeredSettings = 0;

        foreach ($this->settings as $sectionDefinition) {
            foreach ($sectionDefinition->getGroups() as $groupDefinition) {
                $registeredSettings += count($groupDefinition->getSettings());
            }
        }

        if ($registeredSettings > $definedSettings) {
            $event->addAlert(new AlertMessage(
                $this->translator->trans('alert', ['%count%' => $registeredSettings - $definedSettings], 'ws_cms_setting'),
                'fa-cogs'
            ));
        }
    }

    public function get(string $setting): ?string
    {
        if (isset($this->settingValues[$setting])) {
            return $this->settingValues[$setting];
        } else {
            $settingDefinition = null;
            foreach ($this->settings as $sectionDefinition) {
                foreach ($sectionDefinition->getGroups() as $groupDefinition) {
                    foreach ($groupDefinition->getSettings() as $settingDef) {
                        if ($settingDef->getCode() === $setting) {
                            $settingDefinition = $settingDef;
                        }
                    }
                }
            }

            if ($settingDefinition !== null) {
                return $settingDefinition->getDefault();
            }
        }

        return null;
    }

    public function save(SectionDefinition $section, string $settingCode, $value): void
    {
        // get setting definition
        $settingDefinition = null;
        foreach ($section->getGroups() as $groupDef) {
            foreach ($groupDef->getSettings() as $settingDef) {
                if ($settingDef->getCode() === $settingCode) {
                    $settingDefinition = $settingDef;
                    break;
                }
            }
        }

        // do not save if setting not found
        if ($settingDefinition === null) {
            return;
        }

        // do not save empty required settings
        if (!$value && $settingDefinition->isRequired()) {
            return;
        }

        $setting = $this->registry->getRepository(Setting::class)->findOneBy([
            'name' => $settingCode,
            'domain' => $this->contextService->getDomain()
        ]);

        if ($setting instanceof Setting) {
            $setting->setValue($value);
        } else {
            $setting = new Setting();
            $setting
                ->setName($settingCode)
                ->setValue($value)
                ->setDomain($this->contextService->getDomain())
            ;

            $this->registry->getManager()->persist($setting);
        }

        $this->registry->getManager()->flush();
    }

    public function clearSettings(): void
    {
        $this->settingValues = null;
    }

    public function loadSettings(): void
    {
        if ($this->settingValues === null) {
            $this->settingValues = [];

            $conn = $this->registry->getConnection();
            $stmt = $conn->prepare('SELECT * FROM ws_setting WHERE setting_domain = :domain');
            $stmt->execute(['domain' => $this->contextService->getDomain()->getId()]);

            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $this->settingValues[$row['setting_name']] = $row['setting_value'];
            }
        }
    }
}
