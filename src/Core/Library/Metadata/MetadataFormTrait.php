<?php

namespace WS\Core\Library\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

trait MetadataFormTrait
{
    protected function addMetadataFields(FormBuilderInterface $builder)
    {
        $builder
            ->add('metadataTitle', TextType::class, [
                'label' => 'metadata.metadataTitle.label',
                'translation_domain' => 'ws_cms',
            ])
            ->add('metadataDescription', TextType::class, [
                'label' => 'metadata.metadataDescription.label',
                'translation_domain' => 'ws_cms',
            ])
            ->add('metadataKeywords', TextType::class, [
                'label' => 'metadata.metadataKeywords.label',
                'translation_domain' => 'ws_cms',
            ])
        ;
    }
}
