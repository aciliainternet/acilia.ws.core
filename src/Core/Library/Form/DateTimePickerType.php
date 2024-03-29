<?php

namespace WS\Core\Library\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateTimePickerType extends AbstractType
{
    public const DATE_TIME_PICKER_ATTR = [
        'data-component' => 'ws_datepicker',
        'data-format' => 'date_hour',
        'data-default-hour' => '0',
        'data-mobile-support' => 'enabled'
    ];

    public function __construct(protected TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => self::DATE_TIME_PICKER_ATTR,
            'html5' => false,
            'widget' => 'single_text',
            'format' => $this->translator->trans('symfony_date_hour_format', [], 'ws_cms'),
        ]);
    }

    public function getParent(): ?string
    {
        return DateTimeType::class;
    }
}
