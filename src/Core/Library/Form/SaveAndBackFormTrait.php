<?php

namespace WS\Core\Library\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

trait SaveAndBackFormTrait
{
    protected function addSaveAndBackField(FormBuilderInterface $builder)
    {
        $builder
            ->add('saveAndBack', SubmitType::class, [
                'translation_domain' => 'ws_cms',
                'label' => 'save.back',
                'attr' => [
                    'class' => 'c-btn c-btn--border',
                ]
            ])
        ;
    }
}
