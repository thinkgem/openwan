<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('help'); ?>
查看已有的模型，并能够创建新模型。
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$("#models > ul").tabs();
    $("#table_name").change(function() {
        if (this.value != 0)
        {
		    $("#table_detail").load("<?php echo url('generate/getcolumns'); ?>", {
                table: this.value
            });
        }
	});
});
</script>

<!-- BEGIN COL2 -->
<div id="col2">
  <div id="col2_content" class="clearfix">
    <div id="table_detail">
    选择数据表后，该处将显示数据表的字段信息。
    </div>
  </div>
</div>
<!-- END COL2 -->

<!-- BEGIN COL3 -->
<div id="col3">
  <div id="col3_content" class="clearfix">
    <!-- add your content here -->
    <div id="models">
      <ul>
        <li><a href="#tab_list"><span>现有的模型</span></a></li>
        <li><a href="#tab_new"><span>创建新模型</span></a></li>
      </ul>
    </div>
    <div id="tab_list">
      <table class="data full">
        <thead>
          <tr>
            <th nowrap>模型类名称</th>
            <th nowrap>模块</th>
            <th nowrap>使用的数据表</th>
            <th nowrap>文件</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($models as $model): ?>
          <tr>
            <th><?php echo h($model->className()); ?></th>
            <td><?php echo h($model->moduleName() != QReflection_Application::DEFAULT_MODULE_NAME ? $model->moduleName() : '-'); ?></td>
            <td><?php echo h($model->tableName()); ?></td>
            <td><?php echo h($model->filePath()); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div id="tab_new">
      <form name="create_model" action="<?php echo url('generate/newmodel'); ?>" method="post">
        <table class="form-table full">
          <tr>
            <th valign="top"><label for="model_name">模型名称：</label></th>
            <td><input type="text" name="new_model_name" id="new_model_name" class="field" size="40" maxlength="30" />
              <br />
              模型名称只能使用 26 个英文字母。
            </td>
          </tr>
          <tr>
            <th valign="top"><label for="model_name">对应的数据表：</label></th>
            <td><?php echo Q::control('dropdownlist', 'table_name', array('items' => $tables)); ?>
              <br />
              选择模型要使用的数据表。
              <br />
			  选择后，右侧会显示该数据表的字段信息。
            </td>
          </tr>
        </table>
        <p>
          <input type="submit" class="button" name="Submit" value="创建模型" />
        </p>
      </form>
    </div>
  </div>
</div>
<!-- END COL3 -->

<?php $this->_endblock(); ?>
