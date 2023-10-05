<?php

namespace WS\Core\Library\Asset\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use WS\Core\Entity\AssetImage;
use WS\Core\Service\ImageService;

class AssetImageType extends AbstractType
{
    public const ASSET_IMAGE_DISPLAY_MODE_LIST = 'list';
    public const ASSET_IMAGE_DISPLAY_MODE_CROP = 'crop';
    public const ASSET_IMAGE_MAX_SIZE = '25M';
    public const ASSET_IMAGE_DEFAULT_THUMB_SIZE = '300x300';

    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /** @param array<string, array<string, ?object>> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['ws']['entity'] === null) {
            throw new InvalidConfigurationException('The options "ws[entity]" is required.');
        }

        $entity = $options['ws']['entity'];
        $entityClass = \strval(get_class($entity));
        $entityField = $builder->getName();

        $aspectRatiosFractions = $this->imageService->getAspectRatiosForComponent($entityClass, $entityField);
        $minimums = $this->imageService->getMinimumsForComponent($entityClass, $entityField);

        $builder->add('asset', FileType::class, [
            'constraints' => new File([
                'mimeTypes' => array_merge(
                    (new MimeTypes())->getMimeTypes('jpeg'),
                    (new MimeTypes())->getMimeTypes('jpg'),
                    (new MimeTypes())->getMimeTypes('png'),
                    (new MimeTypes())->getMimeTypes('gif'),
                    (new MimeTypes())->getMimeTypes('webp'),
                ),
                'mimeTypesMessage' => 'ws.cms.image.invalid_type',
                'maxSize' => self::ASSET_IMAGE_MAX_SIZE,
                'maxSizeMessage' => 'ws.cms.image.max_size'
            ]),
            'attr' => [
                'data-component' => 'ws_cropper',
                'data-ratios' => json_encode($aspectRatiosFractions),
                'data-minimums' => json_encode($minimums),
                'data-display-mode' => $options['ws']['display-mode'],
                'data-is-visible' => 'false'
            ],
        ]);

        if ($options['ws']['display-mode'] == self::ASSET_IMAGE_DISPLAY_MODE_LIST) {
            $builder->add('asset_data', HiddenType::class, [
                'attr' => [
                    'data-type' => 'ws-asset-data',
                    'data-id' => $options['attr']['id']
                ]
            ]);
        }

        $builder->add('cropper', CropperDataType::class, [
            'ws-ratios' => $minimums,
        ]);

        $builder->add('asset_remove', HiddenType::class);

        $assetImageOptions = [
            'class' => AssetImage::class
        ];

        try {
            // Get Asset Image from Entity
            $fieldGetter = sprintf('get%s', ucfirst($entityField));
            if (method_exists($entity, $fieldGetter)) {
                $ref = new \ReflectionMethod($entity, $fieldGetter);
                $asset = $ref->invoke($entity);
                if ($asset instanceof AssetImage) {
                    $assetImageOptions['data'] = $asset;
                }
            }
        } catch (\Exception $e) {
        }

        $builder->add('asset_image', EntityType::class, $assetImageOptions);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $assetImage = $event->getForm()->get('asset_image');
            /** @var array */
            $submittedData = $event->getData();

            if (!array_key_exists('asset_image', $submittedData) && $assetImage->getData() instanceof AssetImage) {
                $submittedData['asset_image'] = $assetImage->getData()->getId();
                $event->setData($submittedData);
            }
        });
    }

    /** @param array<string, array<string, ?object>> $options */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'ws' => [
                'entity' => $options['ws']['entity'],
                'display_mode' => $options['ws']['display-mode'],
                'thumb_size' => $options['ws']['thumb-size'] ?? self::ASSET_IMAGE_DEFAULT_THUMB_SIZE
            ],
            'type' => 'ws-asset-image',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => true,
            'mapped' => false,
            'ws' => [
                'entity' => null,
                'display-mode' => 'list',
                'thumb-size' => self::ASSET_IMAGE_DEFAULT_THUMB_SIZE
            ]
        ]);
    }
}
