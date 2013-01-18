<?php
$properties = array();
foreach ($class->properties as $property)
{
    if ($protected && $property->is_protected)
    {
        $properties[] = $property;
    }
    elseif (!$protected && $property->is_public)
    {
        $properties[] = $property;
    }
}

if (($protected && count($properties) == 0)
    || (!$protected && count($properties) == 0))
{
    return;
}
?>

<div class="summary">

  <h2><?php echo $protected ? '保护的属性' : '公开的属性'; ?></h2>
  <p>
    <a href="#" class="toggle">隐藏继承的属性</a>
  </p>

  <table class="list-table">
    <tr>
      <th>属性</th><th>类型</th><th>描述</th><th>定义于</th>
    </tr>

    <?php foreach ($properties as $property): ?>

    <tr<?php echo $property->is_inherited ? ' class="inherited"' : ''; ?>>
      <td>$<?php echo $property->name; ?></td>
      <td><?php echo $property->type_hint; ?></td>
      <td><?php echo h($property->summary); ?></td>
      <td>
        <?php if ($property->is_inherited): ?>
        <a href="<?php echo Command_API::classUrl($property->declaring_class, $class_url); ?>"><?php echo h($property->declaring_class->name); ?></a>
        <?php else: ?>
        <?php echo h($property->declaring_class->name); ?>
        <?php endif; ?>

      </td>
    </tr>
    <?php endforeach; ?>

  </table>
</div>


