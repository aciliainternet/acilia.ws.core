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
                'attr' => [
                    'class' => 'js-char-count',
                    'maxlength' => 60,
                ],
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ], $options))
            ->add('metadataDescription', TextareaType::class, array_merge([
                'label' => 'metadata.metadataDescription.label',
                'help' => 'metadata.metadataDescription.help',
                'attr' => [
                    'class' => 'js-char-count',
                    'maxlength' => 160,
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
