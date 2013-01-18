<?php $this->_extends('_layouts/index_layout'); ?>

<?php $this->_block('contents'); ?>

<div class="apidoc-index">

  <h1>QeePHP API 参考手册</h1>

  <p>API 参考手册提供了 QeePHP 所有对象和方法的参考信息和用法示例。是日常使用必备的参考文档。</p>
  <p>&nbsp;</p>
  <p><a href="/docs/">阅读更多文档...</a></p>

  <ul id="tabs-packages">
    <?php foreach($packages as $package): ?>
    <li><a href="#package-<?php echo $package->name; ?>"><?php echo $package->name; ?></a></li>
    <?php endforeach; ?>

  </ul>

  <?php foreach($packages as $package): ?>

  <div class="package" id="package-<?php echo $package->name; ?>">

    <h2>包 - <?php echo h($package->name); ?></h2>

    <table class="package-classes">
      <?php $i = 0;  $classes = $package->classes; ksort($classes); foreach($classes as $i => $class): $i++; ?>
      <tr>
        <?php if ($i == 1): ?>
        <td rowspan="<?php echo count($package->classes); ?>" valign="top" class="formatted package-description">
          <?php echo Command_API::formatting($package->description); ?>
        </td>

        <?php endif; ?>

        <td valign="top">
          <p class="class-name">
            <a href="<?php echo Command_API::classUrl($class, $class_url); ?>"><?php echo h($class->name); ?></a>
          </p>
          <p class="class-summary">
            <?php echo h($class->summary); ?>
          </p>
        </td>
      </tr>
      <?php endforeach; ?>

    </table>
  </div>

  <?php endforeach; ?>

</div>

<script type="text/javascript">$("#tabs-packages").tabs();</script>

<?php $this->_endblock(); ?>

