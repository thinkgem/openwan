<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title><?php echo $pathway->getTitle();?> - <?php echo Q::ini('appini/site/title');?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="author" content="thinkgem@163.com"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR;?>css/global.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR;?>css/login.css"/>
        <script type="text/javascript">
            if(self.parent.frames.length != 0) {
                self.parent.location=document.location;
            }
        </script>
    </head>
    <body>        
        <table class="logintb">
            <tr>
                <td class="login"><h1><?php echo Q::ini('appini/title');?>后台</h1>
                    <p> OpenWan 媒体资产管理系统管理登录</p>
                </td>
                <td>
                    <?php $this->_block('contents');?><?php $this->_endblock();?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="copyright">
                        <p><span>Copyright 2009-2010 Powered by OpenWan | </span><a href="mailto:thinkgem@163.com" title="给我发邮件">ThinkGem</a></p>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
