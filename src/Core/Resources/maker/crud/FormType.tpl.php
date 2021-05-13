<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $bounded_full_class_name ?>;
use Symfony\Component\Form\AbstractType;
<?php foreach ($field_type_use_statements as $className): ?>
use <?= $className ?>;
<?php endforeach; ?>
<?php if ($metadata_fields): ?>
use WS\Site\Library\Metadata\MetadataFormTrait;
<?php endif ?>
<?php if ($publishing_fields): ?>
use WS\Core\Library\Publishing\PublishingFormTrait;
<?php endif ?>
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class <?= $class_name ?> extends AbstractType
{
<?php if ($metadata_fields): ?>
    use MetadataFormTrait;
<?php endif ?>
<?php if ($publishing_fields): ?>
    use PublishingFormTrait;
<?php endif ?>

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
<?php foreach ($form_fields as $form_field => $typeOptions): ?>
<?php if (null === $typeOptions['type'] && !$typeOptions['options_code']): ?>
            ->add('<?= $form_field ?>')
<?php elseif (null !== $typeOptions['type'] && !$typeOptions['options_code']): ?>
            ->add('<?= $form_field ?>', <?= $typeOptions['type'] ?>::class)
<?php else: ?>
            ->add('<?= $form_field ?>', <?= $typeOptions['type'] ? ($typeOptions['type'].'::class') : 'null' ?>, [
<?= $typeOptions['options_code']."\n" ?>
            ])
<?php endif; ?>
<?php endforeach; ?>
        ;

<?php if ($metadata_fields): ?>
    $this->addMetadataFieldsFields($builder);
<?php endif ?>
<?php if ($publishing_fields): ?>
    $this->addPublishingFields($builder);
<?php endif ?>
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
<?php if ($bounded_full_class_name): ?>
            'data_class' => <?= $bounded_class_name ?>::class,
<?php endif ?>
            'attr' => [
                'novalidate' => 'novalidate',
                'autocomplete' => 'off',
                'accept-charset'=> 'UTF-8'
            ]
        ]);
    }
}
