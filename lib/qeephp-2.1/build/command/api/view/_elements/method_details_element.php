<?php if(!$class->native_methods_count) return; ?>

<div class="details">

  <h2>方法详细说明</h2>

  <?php foreach($class->methods as $method): ?>
  <?php if($method->is_inherited || $method->is_private) continue; ?>

  <a name="<?php echo h($method->declaring_class->name . '_' . $method->name); ?>"></a>

  <div class="name method">
    <?php echo h($method->name); ?>()
    <span class="tags">方法</span>
  </div>

  <table class="list-table">
    <tr>
      <td colspan="3">
        <span class="signature2">
          <?php echo preg_replace('/\{\{([^\{\}]*?)\|([^\{\}]*?)\}\}\(/', '$2(', $method->signature); ?>
        </span>
      </td>
    </tr>

    <?php if(!empty($method->parameters)): ?>
    <?php foreach($method->parameters as $param): ?>

    <tr>
      <td class="param_name">$<?php echo h($param->name); ?></td>
      <td class="param_type"><?php echo h($param->type_hint); ?></td>
      <td class="param_desc"><?php echo h($param->doc_comment); ?></td>
    </tr>
    <?php endforeach; ?>

    <tr>
      <td class="param_name"><?php echo '{return}'; ?></td>
      <td class="param_type"><?php echo h($method->return_type); ?></td>
      <td class="param_desc"><?php echo h($method->return_comment); ?></td>
    </tr>
    <?php endif; ?>

  </table>

  <div class="formatted">
    <?php echo Command_API::formatting($method->description); ?>
  </div>

  <?php endforeach; ?>

</div>

