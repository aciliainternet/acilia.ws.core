<?php

namespace WS\Core\Library\Maker;

use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Routing\Route;
use Doctrine\Inflector\InflectorFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Input\InputArgument;
use WS\Core\Library\Asset\ImageRenditionInterface;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use WS\Site\Library\Metadata\MetadataProviderInterface;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MakeCrud extends AbstractMaker
{
    public function __construct(private DoctrineHelper $doctrineHelper)
    {
    }

    public static function getCommandName(): string
    {
        return 'ws:make:crud';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a WS command of different flavors';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates CRUD for Doctrine entity class')
            ->addArgument(
                'entity-class',
                InputArgument::REQUIRED,
                sprintf(
                    'The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)',
                    Str::asClassName(Str::getRandomTerm())
                )
            )
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'Ask questions about generation of fields');

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(Route::class, 'router');

        $dependencies->addClassDependency(AbstractType::class, 'form');

        $dependencies->addClassDependency(Validation::class, 'validator');

        $dependencies->addClassDependency(TwigBundle::class, 'twig-bundle');

        $dependencies->addClassDependency(DoctrineBundle::class, 'orm-pack');

        $dependencies->addClassDependency(CsrfTokenManager::class, 'security-csrf');

        $dependencies->addClassDependency(ParamConverter::class, 'annotations');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $isInteractive = $input->getOption('interactive');

        if ($isInteractive) {
            $helper = new SymfonyQuestionHelper();
        }

        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists(
                strval($input->getArgument('entity-class')),
                $this->doctrineHelper->getEntitiesForAutocomplete()
            ),
            'Entity\\'
        );

        /** @var EntityDetails */
        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $fieldTypeUseStatements = [];
        $formFields = [];
        $listFields = [];
        $entityFields = [];
        $associationFields = [];
        $interfaceFields = [];
        $imageFields = [];

        $metadataFields = false;
        $publishingFields = false;

        $entityFormFields = $entityDoctrineDetails->getFormFields();

        /** @var ClassMetadata */
        $classMetadata = $this->doctrineHelper->getMetadata($entityClassDetails->getFullName());
        $associationFieldNames = $classMetadata->getAssociationNames();

        foreach ($associationFieldNames as $associationFieldName) {
            $associationFields[$associationFieldName] = $classMetadata->getAssociationTargetClass($associationFieldName);
        }

        foreach ($entityFormFields as $name => $fieldTypeOptions) {

            // remove internal fields
            if (in_array($name, ['domain', 'createdBy', 'modifiedAt', 'createdAt'])) {
                unset($entityFormFields[$name]);
                continue;
            } elseif (in_array($name, ['metadataTitle', 'metadataDescription', 'metadataKeywords'])) {
                unset($entityFormFields[$name]);
                $metadataFields = true;
                $interfaceFields[] = Str::getShortClassName(MetadataProviderInterface::class);
                continue;
            } elseif (in_array($name, ['publishStatus', 'publishSince', 'publishUntil'])) {
                unset($entityFormFields[$name]);
                $publishingFields = true;
                continue;
            }

            $fieldTypeOptions ??= ['type' => null, 'options_code' => null];

            if (in_array($name, $associationFieldNames)) {
                switch ($associationFields[$name]) {
                    case 'WS\Core\Entity\AssetImage':
                        $fieldTypeOptions['type'] = 'WS\Core\Library\Asset\Form\AssetImageType';
                        $interfaceFields[] = Str::getShortClassName(ImageRenditionInterface::class);
                        $imageFields[] = $name;
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

        $filterFields = array_diff($listFields, $associationFieldNames);

        if ($isInteractive) {
            $questionFilter = new ChoiceQuestion(
                'Please select filterFields for repository (default all)',
                $filterFields,
                implode(',', range(0, (count($filterFields) - 1)))
            );
            $questionFilter->setMultiselect(true);

            $filterFields = $helper->ask($input, $io, $questionFilter);
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
                implode(',', range(0, (count($listFields) - 1)))
            );
            $questionList->setMultiselect(true);
            $listFields = (array) $helper->ask($input, $io, $questionList);

            $questionSort = new ChoiceQuestion(
                'Please select sortFields for service (default all)',
                $listFields,
                implode(',', range(0, (count($listFields) - 1)))
            );
            $questionSort->setMultiselect(true);
            $sortFields = $helper->ask($input, $io, $questionSort);
        } else {
            $sortFields = $listFields;
        }


        $interfaceFields = array_unique($interfaceFields);
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
                'interface_fields' => $interfaceFields,
                'image_fields' => $imageFields,
                'metadata_provider_interface' => Str::getShortClassName(MetadataProviderInterface::class),
                'image_rendition_interface' => Str::getShortClassName(ImageRenditionInterface::class),
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
            sprintf(
                'Next: Check your new CRUD by going to <fg=yellow>%s/</>',
                Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix())
            ),
            sprintf(
                'Remember to add ROLE_%s_APP roles to <fg=yellow>%s/config/packages/security.yaml</>',
                $entitySnakeCaseName,
                $generator->getRootDirectory()
            )
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
        /** @var string */
        $replace = preg_replace(
            [
                '#([A-Z][a-z]*)(\d+[A-Z][a-z]*\d+)#',
                '#([A-Z]+\d*)([A-Z])#',
                '#([a-z]+\d*)([A-Z])#',
                '#([^_\d])([A-Z][a-z])#'
            ],
            '$1_$2',
            $camelCase
        );
        return strtolower(
            $replace
        );
    }
}
