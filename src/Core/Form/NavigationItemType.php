<?php

namespace WS\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavigationItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parent', ChoiceType::class, [
                'label' => 'form.parent.label',
                'choices' => $this->getNavigationChoices($options),
                'choice_value' => 'value',
                'choice_label' => 'label',
                'attr' => [
                    'placeholder' => 'form.parent.placeholder',
                ],
            ])
            ->add('item', ChoiceType::class, [
                'label' => 'form.item.label',
                'choices' => $this->getNavigationEntitiesChoices($options),
                'choice_value' => 'value',
                'choice_label' => 'label',
                'attr' => [
                    'placeholder' => 'form.item.placeholder',
                    'data-search' => true,
                ],
            ])
        ;
    }

    private function getNavigationEntitiesChoices(array $options): array
    {
        $out = [];

        /** @var NavigationEntitiesModel $entity */
        foreach ($options['navigation_entities'] as $entityList) {
            $group = $entityList->getName();

            foreach ($entityList->getItems() as $item) {
                $out[] = [
                    'group_by' => $group,
                    'label' => $entity->getLabel(),
                    'value' => $entity->getId(),
                ];
            }
        }

        return $out;
    }

    private function getNavigationChoices(array $options): array
    {
        return $this->getChoicesForParent($options['navigation_tree']);
    }

    private function getChoicesForParent(array $tree): array
    {
        $out = [];

        foreach ($tree['children'] as $item) {
            $out[] = [
                'label' => $item['entity']->getLabel(),
                'value' => $item['entity']->getId(),
            ];

            if (count($item['children'])) {
                array_push($out, ...$this->getChoicesForParent($item));
            }
        }

        return $out;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'navigation_tree' => [],
            'navigation_entities' => [],
            'edit' => false,
            'mapped' => false,
            'attr' => [
                'novalidate' => 'novalidate',
                'autocomplete' => 'off',
                'accept-charset' => 'UTF-8',
            ],
        ]);
    }
}
