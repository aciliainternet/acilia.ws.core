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

create_success: <?= $entity_name ?> created
create_error: There was an error when creating the <?= $entity_name ?>, please try again.
edit_success: <?= $entity_name ?> edited
edit_error: There was an error when editing the <?= $entity_name ?>, please try again.
delete_warning: Are you sure you want to delete this <?= $entity_name ?>?
delete_success: <?= $entity_name ?> deleted successfully
no_rows_found: No <?= $entity_name_plural ?> available
not_found: Unable to find the <?= $entity_name ?> with id %s
