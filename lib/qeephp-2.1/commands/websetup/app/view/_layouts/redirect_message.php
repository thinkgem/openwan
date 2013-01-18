<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>WebSetup for QeePHP</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link href="<?php echo url('static/css'); ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="<?php echo url('static/js'); ?>"></script>
</head>
<body>
<div id="page_margins">

  <!-- BEGIN PAGE -->

  <div id="page">

    <!-- BEGIN HEADER -->

    <div id="header">
      <div id="topnav">
        <a href="<?php echo $_BASE_DIR; ?>/public/" target="_blank">访问我的应用程序</a>
      </div>
      <h1>WebSetup for QeePHP</h1>
    </div>

    <!-- END HEADER -->

	<!-- BEGIN NAV -->

    <div id="nav">
      <!-- skiplink anchor: navigation -->
      <a id="navigation" name="navigation"></a>
      <div id="nav_main">
        <?php echo $this->_control('navmain', 'navmain'); ?>
      </div>
    </div>

    <!-- END NAV -->

    <!-- BEGIN MAIN -->

    <div id="main">

      <!-- BEGIN COL1 -->

      <div id="col1">
        <div id="col1_content" class="clearfix">
          <!-- add your content here -->
          <?php echo $this->_control('submenu', 'submenu'); ?>
          <p id="help_text" class="note">
            <?php $this->_block('help'); ?><?php $this->_endblock(); ?>
          </p>
        </div>
      </div>

      <!-- END COL1 -->

      <!-- BEGIN CONTENTS_FOR_LAYOUTS -->
	  <div id="col3_full">
	    <div id="col3_content" class="clearfix">
          <div id="redirect_message">
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
        </div>
      </div>

      <!-- END CONTENTS_FOR_LAYOUTS -->

      <!-- IE Column Clearing -->
      <div id="ie_clearing"> &#160; </div>
    </div>

    <!-- END MAIN -->

  </div>

  <!-- END PAGE -->

  <!-- BEGIN FOOTER -->

  <div id="footer">
    <p>
      WebSetup for <a href="http://www.qeephp.org/" target="_blank">QeePHP</a> |
      Layout based on <a href="http://www.yaml.de/" target="_blank">YAML</a> |
      运行时间：<span id="runtime_info_elapsed_time"></span> |
      特别感谢 <a href="http://www.fleaphp.org/bbs/viewthread.php?tid=3266" target="_blank">dos2000 创建的 WebSetup for FleaPHP 应用</a>
    </p>
  </div>

  <!-- END FOOTER -->

</div>
</body>
</html>

