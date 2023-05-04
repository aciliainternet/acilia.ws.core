<?php

namespace WS\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WS\Core\Library\Navigation\NavigationEntityItemInterface;
use WS\Core\Service\NavigationService;

class NavigationItemType extends AbstractType
{
    public function __construct(private NavigationService $navigationService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parent', ChoiceType::class, [
                'label' => 'form.parent.label',
                'choices' => $this->getNavigationChoices($options),
                'choice_translation_domain' => false,
                'attr' => [
                    'placeholder' => 'form.parent.placeholder',
                ],
            ])
            ->add('item', ChoiceType::class, [
                'label' => 'form.item.label',
                'choices' => $this->getNavigationEntitiesChoices($options),
                'choice_translation_domain' => false,
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

            $out[$group] = [];
            /** @var NavigationEntityItemInterface $item */
            foreach ($entityList->getItems() as $item) {
                $label = $this->navigationService->getNavigationEntityLabel($item);
                $out[$group][$label] = $item->getId();
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
            /** @var NavigationEntityItemInterface */
            $entity = $item['entity'];
            $label = $this->navigationService->getNavigationEntityLabel($entity);
            $out[$label] = $entity->getId();

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
