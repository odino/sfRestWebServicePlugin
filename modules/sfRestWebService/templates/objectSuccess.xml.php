<?xml version="1.0" encoding="utf-8"?>
<object id="<?php echo $object['id'] ?>">
  <?php foreach ($object as $key => $value): ?>
    <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
  <?php endforeach ?>
</object>