
<div id="sidebar">
  <ul id="sidebar-items">
    <li>
      <form id="search" action="http://www.google.com/search" method="get" target="_blank">
        <input type="hidden" name="hl" value="zh-CN" />
        <input type="text" id="search-text" name="q" value="" />
        <input type="submit" value="搜索" />
        OpenWan 网站
      </form>
    </li>
    <?php if (!empty($currentUser)): ?>
    <li>
        <h3>欢迎使用</h3>
        <ul class="links">
            <li>你好，<?php echo $currentUser['username'];?> 欢迎回来！</li>
            <li><a href="<?php echo url('admin::default/index');?>">进入后台</a></li>
        </ul>
    </li>
    <?php else: ?>
    <li>
        <h3>快捷登陆</h3>        
        <form name="login_form" action="<?php echo url('admin::default/login');?>" method="post">
          <ul class="links">
              <li>用户名：<input type="text" name="username" style="width:100px;" value="admin"/></li>
              <li>密　码：<input type="password" name="password" style="width:100px;" value="admin"/></li>
              <li style="text-align:center;"><input type="submit" value="  登录  "/></li>
          </ul>
        </form>
    </li>
    <li>
        <h3>欢迎登陆</h3>
        <a href="<?php echo url('admin::default/login');?>">登录后台</a>
    </li>
    <?php endif; ?>
    <li>
      <h3>加入社区</h3>
      <ul class="links">
        <li><a href="http://openwan.com/" target="_blank">OpenWan 官方网站</a></li>
        <li><a href="http://openwan.com/bbs/" target="_blank">论坛</a></li>
        <li><a href="http://code.google.com/p/openwan/issues/list" target="_blank">Bug 报告</a></li>
      </ul>
    </li>
    <li>
      <h3>浏览文档</h3>
      <ul class="links">
        <li><a href="http://openwan.com/docs" target="_blank">OpenWan 文档</a></li>
      </ul>
    </li>
  </ul>
</div>
