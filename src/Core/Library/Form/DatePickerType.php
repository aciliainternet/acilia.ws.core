<?php

namespace WS\Core\Library\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DatePickerType extends AbstractType
{
    public const DATE_PICKER_ATTR = [
        'data-component' => 'ws_datepicker',
        'data-format' => 'date'
    ];

    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => self::DATE_PICKER_ATTR,
            'html5' => false,
            'widget' => 'single_text',
            'format' => $this->translator->trans('symfony_date_format', [], 'ws_cms'),
        ]);
    }

    public function getParent(): ?string
    {
        return DateTimeType::class;
    }
}
