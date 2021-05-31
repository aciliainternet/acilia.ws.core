# <?= $entityName."\n" ?>
menu: <?= $entityNamePlural."\n" ?>
title: <?= $entityNamePlural."\n" ?>
<?php foreach ($entityFields as $field) :?>
fields.<?= $field ?>.label: <?= $field."\n" ?>
fields.<?= $field ?>.placeholder: <?= ucwords($field) ?> placeholder
<?php endforeach; ?>
<?php foreach ($entityFields as $field) :?>
form.<?= $field ?>.label: <?= ucwords($field)."\n" ?>
form.<?= $field ?>.placeholder: <?= ucwords($field) ?> placeholder
<?php endforeach; ?>

create_success: <?= $entityName ?> created
create_error: There was an error when creating the <?= $entityName ?>, please try again.
edit_success: <?= $entityName ?> edited
edit_error: There was an error when editing the <?= $entityName ?>, please try again.
delete_warning: Are you sure you want to delete this <?= $entityName ?>?
delete_success: <?= $entityName ?> deleted successfully
no_rows_found: No <?= $entityNamePlural ?> available
not_found: Unable to find the <?= $entityName ?> with id %s
