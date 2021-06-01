# <?= $entity_name."\n" ?>
menu: <?= $entity_name_plural."\n" ?>
title: <?= $entity_name_plural."\n" ?>
<?php foreach ($entity_fields as $field) :?>
fields.<?= $field ?>.label: <?= $field."\n" ?>
fields.<?= $field ?>.placeholder: <?= ucwords($field) ?> placeholder
<?php endforeach; ?>
<?php foreach ($entity_fields as $field) :?>
form.<?= $field ?>.label: <?= ucwords($field)."\n" ?>
form.<?= $field ?>.placeholder: <?= ucwords($field) ?> placeholder
<?php endforeach; ?>

create_success: <?= $entity_name ?> creado
create_error: Ha ocurrido un problema al crear el <?= $entity_name ?>, por favor inténtelo de nuevo
edit_success: <?= $entity_name ?> editado
edit_error: Ha ocurrido un problema al editar el <?= $entity_name ?>, por favor inténtelo de nuevo
delete_warning: ¿Seguro que desea eliminar este <?= $entity_name ?>?
delete_success: <?= $entity_name ?> eliminado correctamente
no_rows_found: No hay <?= $entity_name_plural ?> para mostrar
not_found: No se pudo encontrar el <?= $entity_name ?> con identificador %s
