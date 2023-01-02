<?php

namespace WS\Core\Library\Publishing;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use WS\Core\Library\Form\DateTimePickerType;

trait PublishingFormTrait
{
    protected function addPublishingFields(FormBuilderInterface $builder)
    {
        $this->addPublishingFieldStatus($builder, true);
        $this->addPublishingFieldDates($builder);
    }

    protected function addPublishingFieldStatus(FormBuilderInterface $builder, bool $required)
    {
        $publishingOptions = [
            'publishing.publishStatus.draft.label' => PublishingEntityInterface::STATUS_DRAFT,
            'publishing.publishStatus.published.label' => PublishingEntityInterface::STATUS_PUBLISHED,
            'publishing.publishStatus.unpublished.label' => PublishingEntityInterface::STATUS_UNPUBLISHED,
        ];

        $builder
            ->add('publishStatus', ChoiceType::class, [
                'translation_domain' => 'ws_cms',
                'label' => 'publishing.publishStatus.label',
                'choices' => $publishingOptions,
                'required' => $required,
                'attr' => [
                    'placeholder' => 'publishing.publishStatus.placeholder',
                    'data-controller' => 'ws-select',
                ],
            ])
        ;
    }

    protected function addPublishingFieldDates(FormBuilderInterface $builder)
    {
        $builder
            ->add('publishSince', DateTimePickerType::class, [
                'translation_domain' => 'ws_cms',
                'label' => 'publishing.publishSince.label',
                'attr' => array_merge(DateTimePickerType::DATE_TIME_PICKER_ATTR, [
                    'placeholder' => 'publishing.publishSince.placeholder',
                ]),
                'row_attr' => [
                    'class' => 'l-form__item--medium',
                ],
            ])
            ->add('publishUntil', DateTimePickerType::class, [
                'translation_domain' => 'ws_cms',
                'label' => 'publishing.publishUntil.label',
                'attr' => array_merge(DateTimePickerType::DATE_TIME_PICKER_ATTR, [
                    'placeholder' => 'publishing.publishUntil.placeholder',
                ]),
                'row_attr' => [
                    'class' => 'l-form__item--medium',
                ],
            ]);
    }
}
