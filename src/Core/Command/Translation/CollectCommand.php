<?php

namespace WS\Core\Command\Translation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use WS\Core\Entity\TranslationAttribute;
use WS\Core\Entity\TranslationNode;
use WS\Core\Service\TranslationService;

#[AsCommand(
    name: 'ws:translation:collect',
    description: 'Collect the registered translations for the public site'
)]
class CollectCommand extends Command
{
    private EntityRepository $nodesRepository;
    private EntityRepository $attributesRepository;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $em,
        private TranslationService $translationService
    ) {
        $this->nodesRepository = $this->em->getRepository(TranslationNode::class);
        $this->attributesRepository = $this->em->getRepository(TranslationAttribute::class);

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Gather Local App translations
        $appTranslationsPath = sprintf(
            '%s/translations',
            \strval($this->parameterBag->get('kernel.project_dir'))
        );
        $this->gatherTranslations($appTranslationsPath, '');

        // Gather Bundled translations
        foreach ($this->translationService->getSources() as $directory => $source) {
            $this->gatherTranslations($directory, $source);
        }

        return 0;
    }

    protected function gatherTranslations(string $directory, string $source): void
    {
        $finder = new Finder();
        $finder->files()->in($directory)->exclude('cms')->name('/\.yaml/');
        $sourcePrefix = !empty($source) ? ($source . '\.') : '';

        // Discover Nodes
        $discoveredNodes = [];
        $candidateTranslations = [];
        foreach ($finder as $file) {
            preg_match('/^(\w+)\.(\w+)\.yaml$/i', $file->getFilename(), $matches);

            if (isset($matches[1]) && isset($matches[2])) {
                $type = $matches[1];
                if (!isset($discoveredNodes[$type])) {
                    $discoveredNodes[$type] = [];
                }
                if (!isset($candidateTranslations[$type])) {
                    $candidateTranslations[$type] = [];
                }

                unset($matches);

                $translationKeys = array_keys((array) Yaml::parse($file->getContents()));
                foreach ($translationKeys as $key) {
                    if (!in_array($key, $candidateTranslations[$type])) {
                        $candidateTranslations[$type][] = $key;
                    }

                    preg_match(sprintf('/^%stranslation\.(\w+)\.name$/i', $sourcePrefix), (string) $key, $matches);
                    if (isset($matches[1])) {
                        $node = $matches[1];
                        if (!in_array($node, $discoveredNodes[$type])) {
                            $discoveredNodes[$type][] = $node;
                        }
                        unset($matches);
                    }
                }
            }
        }

        // Discover Translations
        $discoveredTranslations = [];
        foreach ($discoveredNodes as $type => $nodes) {
            foreach ($nodes as $node) {
                foreach ($candidateTranslations[$type] as $translationKey) {
                    preg_match(sprintf('/^%s%s\.(.+)$/', $sourcePrefix, $node), (string) $translationKey, $matches);

                    if (isset($matches[1])) {
                        $key = $matches[1];
                        if (preg_match('/^(.+)(\.description)$/', $key) === 0) {
                            if (!isset($discoveredTranslations[$type])) {
                                $discoveredTranslations[$type] = [];
                            }
                            if (!isset($discoveredTranslations[$type][$node])) {
                                $discoveredTranslations[$type][$node] = [];
                            }
                            if (!in_array($key, $discoveredTranslations[$type][$node])) {
                                $discoveredTranslations[$type][$node][] = $key;
                            }
                        }
                    }
                }
            }
        }

        // Processes Discovered Translations
        if (count($discoveredTranslations) > 0) {
            foreach ($discoveredTranslations as $type => $nodes) {
                foreach ($nodes as $node => $translations) {
                    $translationNode = $this->nodesRepository->findOneBy(['name' => $node, 'type' => $type]);
                    if (!$translationNode instanceof TranslationNode) {
                        $translationNode = new TranslationNode();
                        $translationNode
                            ->setName(\strval($node))
                            ->setType(\strval($type))
                            ->setSource($source)
                        ;
                        $this->em->persist($translationNode);
                    }

                    foreach ($translations as $translation) {
                        $translationAttribute = $this->attributesRepository->findOneBy(['node' => $translationNode, 'name' => $translation]);
                        if (!$translationAttribute instanceof TranslationAttribute) {
                            $translationAttribute = new TranslationAttribute();
                            $translationAttribute
                                ->setNode($translationNode)
                                ->setName($translation)
                            ;
                            $this->em->persist($translationAttribute);
                        }
                    }
                }
            }
            $this->em->flush();
        }
    }
}
