<?xml version="1.0" encoding="utf-8"?>
<objects>
<?php foreach ($objects as $object): ?>
  <object id="<?php echo $object['id'] ?>">
<?php foreach ($object as $key => $value): ?>
    <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
<?php endforeach ?>
  </object>
<?php endforeach ?>
</objects>