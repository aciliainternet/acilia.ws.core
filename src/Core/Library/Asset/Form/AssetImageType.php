<?php

namespace WS\Core\Library\Asset\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use WS\Core\Entity\AssetImage;
use WS\Core\Service\ImageService;

class AssetImageType extends AbstractType
{
    const ASSET_IMAGE_DISPLAY_MODE_LIST = 'list';
    const ASSET_IMAGE_DISPLAY_MODE_CROP = 'crop';

    const ASSET_IMAGE_MIME_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const ASSET_IMAGE_MAX_SIZE = '25M';

    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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
                'mimeTypes' => self::ASSET_IMAGE_MIME_TYPES,
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
            $submittedData = $event->getData();

            if (!array_key_exists('asset_image', $submittedData) && $assetImage->getData() instanceof AssetImage) {
                $submittedData['asset_image'] = $assetImage->getData()->getId();
                $event->setData($submittedData);
            }
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'ws' => [
                'entity' => $options['ws']['entity'],
                'display_mode' => $options['ws']['display-mode']
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
                'display-mode' => 'list'
            ]
        ]);
    }
}
