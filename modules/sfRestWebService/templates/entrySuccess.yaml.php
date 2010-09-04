<?php foreach ($objects as $object): ?>
-
<?php foreach ($object as $key => $value): ?>
  <?php echo $key ?>: <?php echo sfYaml::dump($value) ?>

<?php endforeach ?>
<?php endforeach ?>