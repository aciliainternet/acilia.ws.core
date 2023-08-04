<?php

namespace WS\Core\Library\Form;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as BaseEntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityType extends BaseEntityType
{
    /** @param array<string, array<string, ?object>> $options */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'ws' => [
                'path' => $options['ws']['path'],
                'formClass' => $options['ws']['formClass']
            ],
            'class' => $options['class'],
            'type' => 'ws-entity-type',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('ws',[
            'formClass' => null,
            'path' => null
        ]);

        parent::configureOptions($resolver);
    }
}