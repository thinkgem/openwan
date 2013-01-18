
<table class="data full">
  <thead>
    <tr>
      <th><?php echo h($table_name); ?> 表的字段</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($columns as $f): ?>
    <tr>
      <th><?php echo h($f['name']); ?> <?php echo h(strtoupper($f['type'])); ?>(<?php echo $f['length']; ?>)</th>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
