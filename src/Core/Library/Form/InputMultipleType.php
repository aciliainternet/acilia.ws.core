<?php

namespace WS\Core\Library\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputMultipleType extends AbstractType
{
    public const INPUT_MULTIPLE_TYPE_ATTR = [
        'data-component' => 'ws_input-multiple',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'required' => false,
            'attr' => self::INPUT_MULTIPLE_TYPE_ATTR
        ]);
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
