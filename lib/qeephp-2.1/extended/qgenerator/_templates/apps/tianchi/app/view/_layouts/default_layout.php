<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>QeePHP: 新一代的敏捷开发框架<?php $this->_block('title'); ?><?php $this->_endblock(); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR; ?>css/style.css">
</head>
<body>

<div id="page">

  <?php $this->_element('sidebar'); ?>

  <div id="content">

<?php $this->_block('contents'); ?><?php $this->_endblock(); ?>

  </div>
  <div id="footer">
    <p>
      Powered by <a href="http://qeephp.com/" target="_blank">QeePHP <?php echo Q::version(); ?></a>
      |
      <a href="http://www.qeeyuan.com/" target="_blank">起源科技</a>
    </p>
  </div>
</div>

</body>
</html>
