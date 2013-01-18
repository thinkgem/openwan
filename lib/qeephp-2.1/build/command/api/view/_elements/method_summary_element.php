<?php
$methods = array();
foreach ($class->methods as $method)
{
    if ($protected && $method->is_protected)
    {
        $methods[] = $method;
    }
    elseif (!$protected && $method->is_public)
    {
        $methods[] = $method;
    }
}

if (($protected && count($methods) == 0)
    || (!$protected && count($methods) == 0))
{
    return;
}
?>

<div class="summary">
  <h2><?php echo $protected ? '保护的方法' : '公共方法'; ?></h2>
  <p>
    <a href="#" class="toggle">隐藏继承的方法</a>
  </p>

  <table class="list-table">
    <tr>
      <th>方法</th><th>描述</th><th>定义于</th>
    </tr>

    <?php foreach($methods as $method): ?>
    <tr<?php echo $method->is_inherited ? ' class="inherited"' : ''; ?>>
      <td>
        <a href="<?php if ($method->is_inherited): echo Command_API::classUrl($method->declaring_class, $class_url); endif; ?>#<?php echo h($method->declaring_class->name . '_' . $method->name); ?>"><?php echo h($method->name . '()'); ?></a>
      </td>

      <td><?php echo h($method->summary); ?></td>
      <td>
        <?php if ($method->is_inherited): ?>
        <a href="<?php echo Command_API::classUrl($method->declaring_class, $class_url); ?>"><?php echo h($method->declaring_class->name); ?></a>
        <?php else: ?>
        <?php echo h($method->declaring_class->name); ?>
        <?php endif; ?>

      </td>
    </tr>
    <?php endforeach; ?>

  </table>
</div>


