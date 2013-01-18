
<ul id="submenu">
  <li id="title"><?php echo $main['title']; ?></li>
  <?php foreach ($main['items'] as $item): ?>
  <li<?php if ($menu->compare($item, $current)): ?> id="active"<?php endif; ?>>
    <a href="<?php echo url("{$item['namespace']}::{$item['controller']}/{$item['action']}"); ?>"><?php echo h($item['title']); ?></a>
  </li>
  <?php endforeach; ?>
</ul>
