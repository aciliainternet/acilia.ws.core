<?php
namespace WS\Core\Form;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtendedEntityType extends EntityType
{

    /** @param array<string, array<string, ?object>> $options */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'ws' => [
                'entity' => $options['ws']['entity']
            ],
            'class' => $options['class'],
            'type' => 'ws-extended-entity',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('ws',[
            'entity' => null
        ]);
        parent::configureOptions($resolver);
    }
}