<?php

namespace WS\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WS\Core\Entity\Navigation;
use WS\Core\Library\Form\ToggleChoiceType;

class NavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.name.label',
                'attr' => [
                    'placeholder' => 'form.name.placeholder',
                ],
            ])
            ->add('default', ToggleChoiceType::class, [
                'label' => 'form.type.label',
                'choices' => [
                    'form.type.default' => 1,
                    'form.type.non_default' => 0,
                ],
                'empty_data' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Navigation::class,
            'edit' => false,
            'attr' => [
                'novalidate' => 'novalidate',
                'autocomplete' => 'off',
                'accept-charset' => 'UTF-8',
            ],
        ]);
    }
}
