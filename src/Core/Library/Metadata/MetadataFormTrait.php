<?php

namespace WS\Core\Library\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

trait MetadataFormTrait
{
    protected function addMetadataFields(FormBuilderInterface $builder, bool $required = false)
    {
        $builder
            ->add('metadataTitle', TextType::class, [
                'label' => 'metadata.metadataTitle.label',
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ])
            ->add('metadataDescription', TextType::class, [
                'label' => 'metadata.metadataDescription.label',
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ])
            ->add('metadataKeywords', TextType::class, [
                'label' => 'metadata.metadataKeywords.label',
                'required' => $required,
                'translation_domain' => 'ws_cms',
            ]);
    }
}
