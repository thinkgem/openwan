
<table class="summary-table" cellpadding="0" cellspacing="0" >
  <tr>
    <th nowrap="nowrap">包</th>
    <td><?php echo h($class->package); ?></td>
  </tr>
  <tr>
    <th nowrap="nowrap">类层次</th>
    <td>
    <?php
    $c = $class;
    $arr = array();

    while (!is_null($c->parent))
    {
        array_push($arr, $c->parent);
        $c = $c->parent;
    }

    if (is_null($class->parent)) echo "class ";
    echo h($class->name);
    echo "\n";

    foreach ($arr as $c)
    {
        echo "&raquo;\n";
        echo '<a href="';
        echo Command_API::classUrl($c, $class_url);
        echo '">';
        echo h($c->name);
        echo "</a>\n";
    }

    ?>
    </td>
  </tr>

  <?php if(!empty($class->interfaces)): ?>
  <tr>
    <th nowrap="nowrap">实现的接口</th>
    <td><?php // echo $this->renderImplements($class); ?></td>
  </tr>
  <?php endif; ?>

  <?php if(!empty($class->subclasses)): ?>
  <tr>
    <th nowrap="nowrap">继承类</th>
    <td><?php // echo $this->renderSubclasses($class); ?></td>
  </tr>
  <?php endif; ?>

  <?php if(!empty($class->version)): ?>
  <tr>
    <th nowrap="nowrap">版本</th>
    <td><?php echo $class->version; ?></td>
  </tr>
  <?php endif; ?>

</table>

<div class="formatted">
  <?php echo Command_API::formatting($class->description); ?>
</div>

