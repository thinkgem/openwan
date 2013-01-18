
<?php foreach ($packages as $package): ?>

<h3>包 <?php echo h($package); ?></h3>

<ul>
  <?php foreach ($package->classes as $class): ?>
  <li>
    <a href="<?php echo Command_API::classUrl($class, $class_url); ?>" title="<?php echo h($class->name); ?>"><?php echo Command_API::className($class->name, 30); ?></a>
  </li>
  <?php endforeach; ?>

</ul>

<?php endforeach; ?>

