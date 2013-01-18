<form <?php foreach ($form->attrs() as $attr => $value): $value = h($value); echo "{$attr}=\"{$value}\" "; endforeach; ?> class="editform">
  <table class="tb">
  <tbody>
<?php
$_hidden_elements = array();
foreach ($form->elements() as $element):
    if ($element->_ui == 'hidden'){
        $_hidden_elements[] = $element;
        continue;
    }
    $id = $element->id;
?>
    <tr>
      <td nowrap="nowrap">
        <?php if ($element->_label): ?><label <?php if ($element->_ui != 'checkbox' && $element->_ui != 'raido'): ?>for="<?php echo $id; ?><?php endif; ?>" class="lbl"><?php echo h($element->_label); ?>：</label><?php endif; ?>
      </td>
      <td>
        <?php echo Q::control($element->_ui, $id, $element->attrs()); ?><?php if ($element->_req): ?>&nbsp;<span class="req">*</span><?php endif; ?>
        <?php if ($element->_tips): ?>&nbsp;<span class="desc"><?php echo nl2br(h($element->_tips)); ?></span><?php endif; ?>        
        <?php if (!$element->isValid()): ?><br/><span class="error"><?php echo nl2br(h(implode("，", $element->errorMsg()))); ?></span><?php endif; ?>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr class="nobg">
      <td colspan="2">
        <input type="submit" class="btn" name="btnsubmit" value="提交"  />
    <?php if ($form->_reset): ?>
        <input type="reset" name="btn_reset" value="重置" class="btn" />
    <?php endif; ?>
    <?php if ($form->_cancel_url): ?>
        <input type="button" name="btn_cancel" value="取消" onclick="document.location.href='<?php echo h($form->_cancel_url); ?>'; return false;" class="btn" />
    <?php endif; ?>
    <?php if ($form->_return): ?>
        <input type="button" class="btn" name="btn_return" value="返回" onclick="history.go(-1);" />
    <?php endif; ?>
    <?php if (isset($btns)): foreach($btns as $key => $value): ?>
        <input type="button" class="btn" name="btn_" value="<?php echo $key?>" onclick="<?php echo $value?>" />
    <?php endforeach; endif; ?>
    <?php foreach ($_hidden_elements as $element): ?>
        <input type="hidden" name="<?php echo $element->id; ?>" id="<?php echo $element->id; ?>" value="<?php echo h($element->value); ?>" />
    <?php endforeach; ?>
      </td>
    </tr>
 </tfoot>
  </table>
</form>

