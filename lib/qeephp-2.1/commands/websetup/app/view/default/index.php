<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('help'); ?>
在开始使用 WebSetup 之前，请务必仔细核对本页列出的应用程序信息，例如应用程序所在路径等等信息。<br />
<br />
只有当这些信息正确无误时，WebSetup 才能够正常工作。
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

<!-- BEGIN COL3 -->
<div id="col3_full">
  <div id="col3_content" class="clearfix">
    <table class="data full">
      <thead>
        <tr>
          <th width="180">项目</th>
          <th>当前值</th>
          <th>备注</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($app_config as $key => $value): ?>
        <tr>
          <th width="180"><?php echo h($key); ?></th>
          <td><?php echo Helper_Ini::value($value); ?></td>
          <td><?php $descr = isset($ini_descriptions[$key]) ? $ini_descriptions[$key] : '-'; echo nl2br(h($descr)); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<!-- END COL3 -->

<?php $this->_endblock(); ?>
