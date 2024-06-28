<?php

namespace WS\Core\Library\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

trait MetadataFormTrait
{
    protected function addMetadataFields(FormBuilderInterface $builder, bool $required = false, array $options = [])
    {
        $builder
            ->add('metadataTitle', TextareaType::class, array_merge([
                'label' => 'metadata.metadataTitle.label',
                'help' => 'metadata.metadataTitle.help',
                'help_html' => true,
                'attr' => [
                    'data-character-count-target' => 'field',
                    'data-action' => 'keyup->character-count#change',
                ],
                'row_attr' => [
                    'data-controller' => 'character-count',
                    'data-character-count-max-value' => 60
                ],
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ], $options))

            ->add('metadataDescription', TextareaType::class, array_merge([
                'label' => 'metadata.metadataDescription.label',
                'help' => 'metadata.metadataDescription.help',
                'help_html' => true,
                'attr' => [
                    'data-character-count-target' => 'field',
                    'data-action' => 'keyup->character-count#change',
                ],
                'row_attr' => [
                    'data-controller' => 'character-count',
                    'data-character-count-max-value' => 160
                ],
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ], $options))
            ->add('metadataKeywords', TextType::class, array_merge([
                'label' => 'metadata.metadataKeywords.label',
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ], $options));
    }
}
