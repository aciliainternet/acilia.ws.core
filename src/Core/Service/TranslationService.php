<?php

namespace WS\Core\Service;

use Doctrine\Persistence\ManagerRegistry;
use WS\Core\Entity\TranslationAttribute;
use WS\Core\Entity\TranslationValue;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;

class TranslationService
{
    protected array $config;
    protected ManagerRegistry $registry;
    protected TranslatorInterface $translator;
    protected ContextService $contextService;
    protected ?array $translations = null;
    protected array $sources;

    public function __construct(
        array $config,
        TranslatorInterface $translator,
        ManagerRegistry $registry,
        ContextService $contextService
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->translator = $translator;
        $this->contextService = $contextService;
        $this->sources = [];
    }

    public function fillCatalogue(MessageCatalogueInterface $catalogue): void
    {
        if ($this->translations === null) {
            $sql = 'SELECT node_name, node_type, node_source, attrib_name, value_translation '
                 . 'FROM ws_translation_node JOIN ws_translation_attribute ON (node_id = attrib_node) '
                 . '  LEFT JOIN ws_translation_value ON (attrib_id = value_attribute AND (value_domain = :domain OR value_domain IS NULL)) '
                 . 'ORDER BY node_name, attrib_name';

            $conn = $this->registry->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute(['domain' => $this->contextService->getDomain()->getId()]);

            $this->translations = [];
            $result = $stmt->fetchAll();

            foreach ($result as $row) {
                $sourcePrefix = !empty($row['node_source']) ? ($row['node_source'] . '.') : '';
                $id = sprintf('%s%s.%s', $sourcePrefix, $row['node_name'], $row['attrib_name']);

                $this->translations[] = [
                    'id' => $id,
                    'text' => $row['value_translation'],
                    'type' => $row['node_type'],
                ];
            }
        }

        foreach ($this->translations as $translation) {
            if (!empty($translation['text'])) {
                $catalogue->add([$translation['id'] => $translation['text']], $translation['type']);
            }
        }
    }

    public function getForCMS(): array
    {
        $translations = [];

        $sql = 'SELECT node_name, node_type, node_source, attrib_id, attrib_name, value_translation '
             . 'FROM ws_translation_node JOIN ws_translation_attribute ON (node_id = attrib_node) '
             . '  LEFT JOIN ws_translation_value ON (attrib_id = value_attribute AND (value_domain = :domain OR value_domain IS NULL)) '
             . 'ORDER BY node_id, attrib_id';

        $conn = $this->registry->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['domain' => $this->contextService->getDomain()->getId()]);

        $result = $stmt->fetchAll();
        foreach ($result as $row) {
            if (!isset($translations[$row['node_name']])) {
                $translations[$row['node_name']] = [
                    'name' => $row['node_name'],
                    'type' => $row['node_type'],
                    'source' => !empty($row['node_source']) ? ($row['node_source'] . '.') : '',
                    'attributes' => []
                ];
            }

            if (!isset($translations[$row['node_name']]['attributes'][$row['attrib_name']])) {
                $translations[$row['node_name']]['attributes'][$row['attrib_name']] = [
                    'id' => $row['attrib_id'],
                    'name' => $row['attrib_name'],
                    'translation' => !empty($row['value_translation']) ? $row['value_translation'] : null
                ];
            }
        }

        return $translations;
    }

    public function updateTranslations(array $translations): void
    {
        $em = $this->registry->getManager();
        $repositoryAttributes = $this->registry->getRepository(TranslationAttribute::class);
        $repositoryValues = $this->registry->getRepository(TranslationValue::class);

        foreach ($translations as $attributeId => $value) {
            $translationAttribute = $repositoryAttributes->find($attributeId);
            if ($translationAttribute instanceof TranslationAttribute) {
                $translationValue = $repositoryValues->findOneBy(['domain' => $this->contextService->getDomain(), 'attribute' => $attributeId]);
                // If the Translated Value is empty and the Value exists, delete it
                if (empty($value) && $translationValue instanceof TranslationValue) {
                    $em->remove($translationValue);
                // If Value is not empty, create or update the translation
                } elseif (!empty($value)) {
                    if (!$translationValue instanceof TranslationValue) {
                        $translationValue = new TranslationValue();
                        $translationValue
                            ->setDomain($this->contextService->getDomain())
                            ->setAttribute($translationAttribute);
                        $em->persist($translationValue);
                    }
                    $translationValue->setTranslation($value);
                }
            }
        }

        $em->flush();
    }

    public function addSource(string $sourcePath, string $sourceName): self
    {
        if (in_array($sourceName, $this->config['sources'])) {
            $this->sources[$sourcePath] = $sourceName;
        }

        return $this;
    }

    public function getSources(): array
    {
        return $this->sources;
    }
}
