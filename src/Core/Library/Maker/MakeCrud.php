<?php

namespace WS\Core\Library\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Inflector\InflectorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

class MakeCrud extends AbstractMaker
{
    private DoctrineHelper $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public static function getCommandName(): string
    {
        return 'ws:make:crud';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a WS command of different flavors';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates CRUD for Doctrine entity class')
            ->addArgument('entity-class', InputArgument::REQUIRED,
                sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)',
                    Str::asClassName(Str::getRandomTerm())))
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'Ask questions about generation of fields');

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(Route::class, 'router');

        $dependencies->addClassDependency(AbstractType::class, 'form');

        $dependencies->addClassDependency(Validation::class, 'validator');

        $dependencies->addClassDependency(TwigBundle::class, 'twig-bundle');

        $dependencies->addClassDependency(DoctrineBundle::class, 'orm-pack');

        $dependencies->addClassDependency(CsrfTokenManager::class, 'security-csrf');

        $dependencies->addClassDependency(ParamConverter::class, 'annotations');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $isInteractive = $input->getOption('interactive');

        if ($isInteractive) {
            $helper = new SymfonyQuestionHelper();
        }

        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'),
                $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $fieldTypeUseStatements = [];
        $formFields = [];
        $listFields = [];
        $entityFields = [];
        $associationFields = [];

        $metadataFields = false;
        $publishingFields = false;

        $entityFormFields = $entityDoctrineDetails->getFormFields();

        $associationFieldNames = $this->doctrineHelper->getMetadata($entityClassDetails->getFullName())->getAssociationNames();

        foreach ($associationFieldNames as $associationFieldName) {
            $associationFields[$associationFieldName] = $this->doctrineHelper->getMetadata($entityClassDetails->getFullName())->getAssociationTargetClass($associationFieldName);
        }

        foreach ($entityFormFields as $name => $fieldTypeOptions) {

            // remove internal fields
            if (in_array($name, ['domain', 'createdBy', 'modifiedAt', 'createdAt'])) {
                unset($entityFormFields[$name]);
                continue;
            } elseif (in_array($name, ['metadataTitle', 'metadataDescription', 'metadataKeywords'])) {
                unset($entityFormFields[$name]);
                $metadataFields = true;
                continue;
            } elseif (in_array($name, ['publishStatus', 'publishSince', 'publishUntil'])) {
                unset($entityFormFields[$name]);
                $publishingFields = true;
                continue;
            }

            $fieldTypeOptions = $fieldTypeOptions ?? ['type' => null, 'options_code' => null];

            if (in_array($name, $associationFieldNames)) {
                switch ($associationFields[$name]) {
                    case 'WS\Core\Entity\AssetImage':
                        $fieldTypeOptions['type'] = 'WS\Core\Library\Asset\Form\AssetImageType';
                        break;
                    default:
                        $fieldTypeOptions['type'] = null;
                        break;
                }
            }

            if (isset($fieldTypeOptions['type'])) {
                $fieldTypeUseStatements[] = $fieldTypeOptions['type'];
                $fieldTypeOptions['type'] = Str::getShortClassName($fieldTypeOptions['type']);
            }

            $formFields[$name] = $fieldTypeOptions;
            if (null === $fieldTypeOptions['type'] && !$fieldTypeOptions['options_code']) {
                $listFields[] = $name;
            }
        }

        // repository class generation
        $repositoryClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Repository',
            'Repository\\',
            'Repository'
        );

        if ($isInteractive) {
            $questionFilter = new ChoiceQuestion(
                'Please select filterFields for repository (default all)',
                $listFields,
                implode(',', range(0,(count($listFields) - 1)))
            );
            $questionFilter->setMultiselect(true);

            $filterFields = $helper->ask($input, $io, $questionFilter);

        } else {
            $filterFields = $listFields;
        }

        $generator->generateClass(
            $repositoryClassDetails->getFullName(),
            __DIR__ . '/../../Resources/maker/crud/Repository.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'filter_fields' => $filterFields,
                'publishing_fields' => $publishingFields
            ]
        );

        // form type class generation
        $iter = 0;
        do {
            $formClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: '') . 'Type',
                'Form\\CMS\\',
                'Type'
            );
            ++$iter;
        } while (class_exists($formClassDetails->getFullName()));

        sort($fieldTypeUseStatements);

        $generator->generateClass(
            $formClassDetails->getFullName(),
            __DIR__ . '/../../Resources/maker/crud/FormType.tpl.php',
            [
                'bounded_full_class_name' => $entityClassDetails->getFullName(),
                'bounded_class_name' => $entityClassDetails->getShortName(),
                'form_fields' => $formFields,
                'field_type_use_statements' => $fieldTypeUseStatements,
                'metadata_fields' => $metadataFields,
                'publishing_fields' => $publishingFields

            ]
        );

        // service class generations
        $serviceClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Service',
            'Service\\',
            'Service'
        );


        if ($isInteractive) {

            $questionList = new ChoiceQuestion(
                'Please select listFields for service (default all)',
                $listFields,
                implode(',', range(0,(count($listFields) - 1)))
            );
            $questionList->setMultiselect(true);
            $listFields = $helper->ask($input, $io, $questionList);

            $questionSort = new ChoiceQuestion(
                'Please select sortFields for service (default all)',
                $listFields,
                implode(',', range(0,(count($listFields) - 1)))
            );
            $questionSort->setMultiselect(true);
            $sortFields = $helper->ask($input, $io, $questionSort);
        } else {
            $sortFields = $listFields;
        }

        $generator->generateClass(
            $serviceClassDetails->getFullName(),
            __DIR__ . '/../../Resources/maker/crud/Service.tpl.php',
            [
                'type_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_type_name' => $formClassDetails->getShortName(),
                'entity_type_full_class_name' => $formClassDetails->getFullName(),
                'sort_fields' => $sortFields,
                'list_fields' => $listFields,
                'metadata_fields' => $metadataFields,
            ]
        );

        // controller class generation
        $controllerClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Controller',
            'Controller\\CMS\\',
            'Controller'
        );

        $inflector = InflectorFactory::create()->build();
        $entityVarSingular = lcfirst($inflector->singularize($entityClassDetails->getShortName()));

        $generator->generateController(
            $controllerClassDetails->getFullName(),
            __DIR__ . '/../../Resources/maker/crud/Controller.tpl.php',
            [
                'service_class_path' => $serviceClassDetails->getFullName(),
                'service_class_name' => $serviceClassDetails->getShortName(),
                'route_path' => Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()),
                'route_prefix' => $entityVarSingular
            ]
        );

        $entityFields = array_keys($formFields);
        $generator->generateFile(
            sprintf('%s/translations/cms/cms_%s.en.yaml', $generator->getRootDirectory(), $entityVarSingular),
            __DIR__ . '/../../Resources/maker/crud/translations.en.tpl.php',
            [
                'entity_name' => $entityClassDetails->getShortName(),
                'entity_name_plural' => $inflector->pluralize($entityClassDetails->getShortName()),
                'entity_fields' => $entityFields
            ]
        );

        $generator->generateFile(
            sprintf('%s/translations/cms/cms_%s.es.yaml', $generator->getRootDirectory(), $entityVarSingular),
            __DIR__ . '/../../Resources/maker/crud/translations.es.tpl.php',
            [
                'entity_name' => $entityClassDetails->getShortName(),
                'entity_name_plural' => $inflector->pluralize($entityClassDetails->getShortName()),
                'entity_fields' => $entityFields
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $entitySnakeCaseName = strtoupper($this->camelCaseToSnakeCase($entityClassDetails->getShortName()));

        $io->text([
            sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>',
                Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix())),
            sprintf('Remember to add ROLE_%s_APP roles to <fg=yellow>%s/config/packages/security.yaml</>',
                $entitySnakeCaseName, $generator->getRootDirectory())
        ]);

        $io->listing([
            sprintf('ROLE_APP_%s_VIEW', $entitySnakeCaseName),
            sprintf('ROLE_APP_%s_CREATE', $entitySnakeCaseName),
            sprintf('ROLE_APP_%s_EDIT', $entitySnakeCaseName),
            sprintf('ROLE_APP_%s_DELETE', $entitySnakeCaseName),
        ]);
    }

    private function camelCaseToSnakeCase(string $camelCase): string
    {
        return strtolower(
            preg_replace(
                [
                    '#([A-Z][a-z]*)(\d+[A-Z][a-z]*\d+)#',
                    '#([A-Z]+\d*)([A-Z])#',
                    '#([a-z]+\d*)([A-Z])#',
                    '#([^_\d])([A-Z][a-z])#'
                ],
                '$1_$2',
                $camelCase
            )
        );
    }
}
