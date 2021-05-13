<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $type_full_class_name ?>;
use <?= $entity_type_full_class_name ?>;
<?php if ($metadata_fields): ?>
use WS\Site\Library\Metadata\MetadataProviderInterface;
use WS\Site\Library\Metadata\MetadataProviderTrait;
<?php endif ?>
use WS\Core\Library\CRUD\AbstractService;

class <?= $class_name ?> extends AbstractService<?php if ($metadata_fields): ?> implements MetadataProviderInterface<?php endif ?><?= "\n" ?>
{
<?php if ($metadata_fields): ?>
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
            ['name' => '<?php echo $list_fields[0] ?>'],
        ];
    }
}
