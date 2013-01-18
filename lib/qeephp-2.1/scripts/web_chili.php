<?php

if (isset($_POST['appid']))
{
    $error = array();

    $appid = preg_replace('[^a-z0-9_]', '', $_POST['appid']);
    if (!$appid || $appid != $_POST['appid'])
    {
        $error[] = sprintf('设置的应用程序名 "%s" 无效.', $appid);
    }

    $parent_dir = trim($_POST['parent_dir']);
    $p = realpath($parent_dir);
    if (!$parent_dir || $p == dirname(__FILE__) || !is_dir($p))
    {
        $error[] = sprintf('设置的目录名 "%s" 无效.', $parent_dir);
    }
    else
    {
        $parent_dir = $p;
    }

    $tpl_name = trim($_POST['tpl_name']);

    if (empty($error))
    {
        // 创建应用程序
        require dirname(dirname(__FILE__)) . '/library/q.php';
        Q::import(dirname(dirname(__FILE__)) . '/extended');
        ob_start();

        $generator = new QGenerator_Application();
        $generator->generate($appid, $parent_dir, $tpl_name);

        $output = ob_get_clean();
        $appid = $parent_dir = '';
    }
}
else
{
    $error = array();
    $appid = '';
    $parent_dir = '';
    $output = '';
}

$tpl_list = require(dirname(dirname(__FILE__)) . '/extended/qgenerator/_templates/apps/apptpl_list.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>QeePHP 应用程序生成器</title>
</head>
<body>
<p>这个简单的页面可以为你创建一个新的 QeePHP 应用程序。</p>

<?php if (!empty($output)): ?>

    <p style="color: #eee; background-color: #666; border: 1px solid #000; padding: 10px; margin: 10px;">
      <?php echo nl2br(htmlspecialchars($output)); ?>
    </p>

<?php else: ?>

    <?php if (!empty($error)): ?>

        <p style="color: #900;">
          <?php echo implode('<br />', $error); ?>
        </p>

    <?php endif; ?>

    <form id="form1" name="form1" method="post" action="web_chili.php">
      <p>
        <strong>输入应用程序名称：</strong>
        <input name="appid" type="text" id="appid" size="20" maxlength="16" value="<?php echo htmlspecialchars($appid); ?>" />
        <br />
        <span style="color: #999;">应用程序名称只能是字母和数字，以及下划线。</span>
      </p>

      <p>
        <strong>在何处创建应用程序：</strong>
        <input name="parent_dir" type="text" id="parent_id" size="60" maxlength="80" value="<?php echo htmlspecialchars($parent_dir); ?>" />
        <br />
        <span style="color: #999;">新应用程序将放置在该目录的子目录中，目录名就是应用程序名。</span>
      </p>


      <p>
        <strong>要使用的应用程序模板：</strong>
        <br />

        <?php reset($tpl_list); $default = key($tpl_list); foreach ($tpl_list as $tpl): ?>
          <p>
            <input type="radio" name="tpl_name" id="tpl_name_<?php echo $tpl['name']; ?>" value="<?php echo $tpl['name']; ?>" <?php if ($default == $tpl['name']): ?>checked="checked"<?php endif; ?>>
            <label for="tpl_name_<?php echo $tpl['name']; ?>"><?php echo htmlspecialchars($tpl['title']); ?></label><br />
            <span style="padding-left: 2em; color: #999;"><?php echo htmlspecialchars($tpl['description']); ?></span>
          </p>
        <?php endforeach; ?>

      </p>

      <p>
        <label>
        <input type="submit" name="Create" id="Create" value="开始创建" />
        </label>
      </p>
    </form>

<?php endif; ?>

</body>
</html>
