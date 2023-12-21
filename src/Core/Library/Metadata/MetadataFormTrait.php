<?php

namespace WS\Core\Library\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

trait MetadataFormTrait
{
    protected function addMetadataFields(FormBuilderInterface $builder, bool $required = false, array $options = [])
    {
        $builder
            ->add('metadataTitle', TextType::class, array_merge([
                'label' => 'metadata.metadataTitle.label',
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ], $options))
            ->add('metadataDescription', TextType::class, array_merge([
                'label' => 'metadata.metadataDescription.label',
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
