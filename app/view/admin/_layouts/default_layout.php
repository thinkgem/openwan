<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="overflow-y:scroll;">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title><?php echo $pathway->getTitle();?> - <?php echo Q::ini('appini/site/title');?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="author" content="thinkgem@163.com"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR;?>css/global.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR;?>css/admin.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR;?>css/pagination.css"/>
        <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/mztreeview2/jsframework.js"></script>
        <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/jquery.form.js"></script>        
        <script type="text/javascript">var baseDir = "<?php echo $_BASE_DIR;?>";</script>
        <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/global.js"></script>
        <?php $this->_block('head');?><?php $this->_endblock();?>
    </head>
    <body class="bg">
        <div class="header w980">
            <a class="logo" title="OpenWan" href="<?php echo url('admin::default/index');?>"><img src="<?php echo $_BASE_DIR;?>img/logo.gif" alt="" /></a>
            <div class="info">您好 <a href="<?php echo url('admin::usercenter/index'); ?>"><?php echo $currentUser['nickname']; ?></a> [ <a href="<?php echo url('admin::default/logout'); ?>">退出</a> ]</div>
            <div class="nav">
                <ul>
                    <?php foreach(Q::ini('appini/nav/top') as $nav): if ($_app->authorizedUDI($currentUser['roles'],$nav['udi'])):?>
                        <li<?php echo $_ctx->namespace.'::'.$_ctx->controller_name == strtolower($nav['udi']) ? ' class="on"' : '';?>><a href="<?php echo url($nav['udi']);?>"><?php echo $nav['title'];?></a></li>
                    <?php endif;endforeach;?>
                </ul>
            </div>
        </div>
        <div class="clear10"></div>
        <div class="content w980">
            <?php $this->_block('contents');?><?php $this->_endblock();?>            
        </div>
        <div class="clear10"></div>        
        <div class="footer w980">
            <a href="javascript:" onclick="addFavorite(location.href,document.title);" style="cursor:pointer">加入收藏</a>
        <?php foreach(Q::ini('appini/nav/bottom') as $key => $nav): if ($_app->authorizedUDI($currentUser['roles'], $nav['udi'])):?>
            <a href="<?php echo url($nav['udi']);?>"><?php echo $nav['title'];?></a>
        <?php endif;endforeach;?>
            <br/>
            <span>Copyright 2009-2010 Powered by OpenWan | </span><a href="mailto:thinkgem@163.com" title="给我发邮件">ThinkGem</a>
        </div>
    </body>
</html>
