<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('title'); ?> - 欢迎<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

	<div id="header">
	  <h1>开始我的 QeePHP 之旅</h1>
	  <h2>驾驭 QeePHP 从这里开始！</h2>
	</div>

	<div id="getting-started">
	  <h1>快速开始</h1>
	  <h2>如何开始我的应用程序：</h2>
	  <ol>
		<li>
		  <h2>建立数据库，并且修改 <tt>config/database.yaml</tt> 文件</h2>
		  <p>QeePHP 需要知道如何连接数据库。</p>
		</li>
		<li>
		  <h2>使用 <tt>php scripts/generate.php</tt> 来自动创建控制器、模型以及表数据入口</h2>
		  <p>要查看 generate.php 可用的选项，不带参数执行 php scripts/generate.php 即可。</p>
		</li>
		<li>
		  <h2>修改应用程序设置 <tt>config/environment.yaml</tt></h2>
		  <p>这个文件控制了应用程序和 QeePHP 框架的行为。</p>
		</li>
		<li>
		  <h2>修改应用程序启动脚本 <tt>config/boot.php</tt></h2>
		  <p>修改启动脚本确保在正确的位置载入 QeePHP 框架。</p>
		</li>
	  </ol>

      <p class="important">
        以上一切工作皆可在 WebSetup for QeePHP 中完成。<br />
		通过浏览器访问应用程序的 scripts/websetup.php 文件，惊喜等你发现！
      </p>
	</div>

<?php $this->_endblock('contents'); ?>
