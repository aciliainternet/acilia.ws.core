<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $type_full_class_name ?>;
use <?= $entity_type_full_class_name ?>;
<?php if (in_array($metadata_provider_interface, $interface_fields)): ?>
use WS\Site\Library\Metadata\MetadataProviderInterface;
use WS\Site\Library\Metadata\MetadataProviderTrait;
<?php endif ?>
<?php if (in_array($image_rendition_interface, $interface_fields)): ?>
use WS\Core\Library\Asset\ImageRenditionInterface;
use WS\Core\Library\Asset\RenditionDefinition;
<?php endif; ?>
use WS\Core\Library\CRUD\AbstractService;

class <?= $class_name ?> extends AbstractService<?php if (!empty($interface_fields)): ?> implements <?= implode(', ', $interface_fields) ?><?php endif ?><?= "\n" ?>
{
<?php if (in_array($metadata_provider_interface, $interface_fields)): ?>
    use MetadataProviderTrait;
<?php endif ?>

    public function getEntityClass(): string
    {
        return <?= $entity_class_name ?>::class;
    }

    public function getFormClass(): ?string
    {
        return <?= $entity_type_name ?>::class;
    }

    public function getSortFields(): array
    {
        return [<?php array_walk($sort_fields, function(&$x) {$x = "'$x'";}); echo implode(', ', $sort_fields); ?>];
    }

    public function getListFields(): array
    {
        return [
            <?php array_walk($list_fields, function(&$x) {$x = "['name' => '$x'";}); echo implode("],\n\t\t\t", $list_fields) ?>],
        ];
    }

<?php if (in_array($image_rendition_interface, $interface_fields) && !empty($image_fields)): ?>
    public function getRenditionDefinitions(): array
    {
        return [
<?php foreach($image_fields as $image_field): ?>
            new RenditionDefinition(<?= $entity_class_name ?>::class, '<?= $image_field ?>', 'thumb', 300, 300, RenditionDefinition::METHOD_THUMB),
            new RenditionDefinition(<?= $entity_class_name ?>::class, '<?= $image_field ?>', '<?= $image_field ?>', 1280, 720, RenditionDefinition::METHOD_CROP),
<?php endforeach; ?>
        ];
    }
<?php endif; ?>
}
