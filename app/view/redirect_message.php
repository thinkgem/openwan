<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('contents'); ?>

<div id="flash_message">
<h3><?php echo $message_caption; ?></h3>
<p>
  <?php echo nl2br(h($message_body)); ?>
</p>
<p>
  <a href="<?php echo $redirect_url; ?>">如果您的浏览器没有自动跳转，请点击这里</a>
</p>

<script type="text/javascript">
setTimeout("window.location.href ='<?php echo $redirect_url; ?>';", <?php echo $redirect_delay * 1000; ?>);
</script>

<?php echo $hidden_script; ?>

</div>

<?php $this->_endblock(); ?>

