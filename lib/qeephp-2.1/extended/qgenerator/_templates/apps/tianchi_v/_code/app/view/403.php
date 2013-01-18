<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>拒绝访问此页面 (403)</title>
    <style type="text/css">
        body { background-color: #fff; color: #666; text-align: center; font-family: arial, sans-serif; }
        div.dialog {
            width: 25em;
            padding: 0 4em;
            margin: 4em auto 0 auto;
            border: 1px solid #ccc;
            border-right-color: #999;
            border-bottom-color: #999;
        }
        h1 { font-size: 100%; color: #f00; line-height: 1.5em; }
        p.tip { font-size: 12px; color: #aaa; }
    </style>
</head>

<body>
  <div class="dialog">
    <h1>拒绝访问此页面</h1>
    <p>
       请确定您已经登录系统，并且具有访问此页面的权限。
       <br />
       或者联系管理员报告错误。
    </p>
    <p>&nbsp;</p>
    <p class="tip">
      修改 "app/view/403.php" 文件可以定制此出错页面的内容。
      <br />
      <?php echo h($message); ?>
    </p>
  </div>
</body>
</html>
